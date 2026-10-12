<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Tender;
use App\Models\ProjectJob;
use App\Models\Contractor;
use App\Models\ContractorQuotation;
use App\Models\PurchaseOrder;
use PHPUnit\Framework\Attributes\Test;

class ProcurementFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $procurement;
    protected $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->procurement = User::factory()->create();
        $this->procurement->assignRole('Procurement');

        $this->viewer = User::factory()->create();
        $this->viewer->assignRole('Viewer');
    }

    protected function createBaseHierarchy()
    {
        $customer = Customer::create([
            'name' => 'Lanka Electricity Board',
            'billing_address' => 'Colombo 02',
            'tax_number' => 'LEB-9988',
            'contact_person' => 'Sunil Shantha',
        ]);

        $tender = Tender::create([
            'tender_number' => 'TDR-POWER-' . rand(100, 999),
            'name' => 'Substation Wiring',
            'customer_id' => $customer->id,
            'awarded_amount' => 500000,
            'budget' => 600000,
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'status' => Tender::STATUS_OPEN,
        ]);

        $job = ProjectJob::create([
            'tender_id' => $tender->id,
            'customer_id' => $customer->id,
            'name' => 'Cable Laying Sector A',
            'project_value' => 120000,
            'status' => ProjectJob::STATUS_PENDING,
        ]);

        $contractor1 = Contractor::create([
            'name' => 'PowerLine Pvt Ltd',
            'email' => 'contact@powerline.lk',
            'phone' => '0112345678',
            'address' => 'Kelaniya',
            'status' => 'Active',
        ]);

        $contractor2 = Contractor::create([
            'name' => 'ElectroTech Lanka',
            'email' => 'sales@electrotech.lk',
            'phone' => '0118765432',
            'address' => 'Moratuwa',
            'status' => 'Active',
        ]);

        return compact('customer', 'tender', 'job', 'contractor1', 'contractor2');
    }

    #[Test]
    public function procurement_can_submit_quotations_and_select_winning_bid()
    {
        $fixtures = $this->createBaseHierarchy();
        $job = $fixtures['job'];
        $c1 = $fixtures['contractor1'];
        $c2 = $fixtures['contractor2'];

        // 1. Submit quotation for contractor 1 (95,000)
        $q1Response = $this->actingAs($this->procurement)
            ->postJson('/api/quotations', [
                'job_id' => $job->id,
                'contractor_id' => $c1->id,
                'quotation_amount' => 95000,
                'quotation_date' => now()->toDateString(),
                'work_scope' => 'High-voltage trenching',
                'estimated_days' => 30,
            ]);
        $q1Response->assertStatus(201);
        $q1Id = $q1Response->json('id');

        // 2. Submit quotation for contractor 2 (88,000)
        $q2Response = $this->actingAs($this->procurement)
            ->postJson('/api/quotations', [
                'job_id' => $job->id,
                'contractor_id' => $c2->id,
                'quotation_amount' => 88000,
                'quotation_date' => now()->toDateString(),
                'work_scope' => 'Standard trenching',
                'estimated_days' => 25,
            ]);
        $q2Response->assertStatus(201);
        $q2Id = $q2Response->json('id');

        // 3. Award contractor 2 as winning bid
        $selectResponse = $this->actingAs($this->procurement)
            ->postJson("/api/quotations/{$q2Id}/select");

        $selectResponse->assertStatus(200);

        // Verify contractor 2 quotation is SELECTED and contractor 1 is REJECTED
        $this->assertEquals(ContractorQuotation::STATUS_SELECTED, ContractorQuotation::find($q2Id)->status);
        $this->assertEquals(ContractorQuotation::STATUS_REJECTED, ContractorQuotation::find($q1Id)->status);

        // Verify Job was updated with winner info & status became In Progress
        $job->refresh();
        $this->assertEquals($c2->id, $job->selected_contractor_id);
        $this->assertEquals(88000, (float)$job->contractor_quote_amount);
        $this->assertEquals(ProjectJob::STATUS_IN_PROGRESS, $job->status);
    }

    #[Test]
    public function purchase_order_lifecycle_follows_allowed_state_machine()
    {
        $fixtures = $this->createBaseHierarchy();
        $customer = $fixtures['customer'];
        $tender = $fixtures['tender'];
        $job = $fixtures['job'];

        $poNumber = 'PO-SLT-' . rand(1000, 9999);

        // 1. Create Draft PO
        $createResponse = $this->actingAs($this->procurement)
            ->postJson('/api/purchase-orders', [
                'po_number' => $poNumber,
                'po_date' => now()->toDateString(),
                'po_description' => 'Procurement electrical goods',
                'po_amount' => 60000,
                'billing_address' => 'Headquarters, Colombo',
                'job_id' => $job->id,
                'tender_id' => $tender->id,
                'customer_id' => $customer->id,
                'status' => PurchaseOrder::STATUS_DRAFT,
            ]);

        $createResponse->assertStatus(201);
        $poId = $createResponse->json('id');
        $po = PurchaseOrder::find($poId);
        $this->assertEquals(PurchaseOrder::STATUS_DRAFT, $po->status);

        // 2. Draft -> Approved (Valid)
        $this->actingAs($this->procurement)
            ->putJson("/api/purchase-orders/{$poId}", [
                'po_date' => now()->toDateString(),
                'po_description' => 'Procurement electrical goods',
                'billing_address' => 'Headquarters, Colombo',
                'status' => PurchaseOrder::STATUS_APPROVED,
            ])
            ->assertStatus(200);

        $this->assertEquals(PurchaseOrder::STATUS_APPROVED, $po->fresh()->status);

        // 3. Approved -> Sent (Valid)
        $this->actingAs($this->procurement)
            ->putJson("/api/purchase-orders/{$poId}", [
                'po_date' => now()->toDateString(),
                'po_description' => 'Procurement electrical goods',
                'billing_address' => 'Headquarters, Colombo',
                'status' => PurchaseOrder::STATUS_SENT,
            ])
            ->assertStatus(200);

        $this->assertEquals(PurchaseOrder::STATUS_SENT, $po->fresh()->status);

        // 4. Sent -> Received (Valid)
        $this->actingAs($this->procurement)
            ->putJson("/api/purchase-orders/{$poId}", [
                'po_date' => now()->toDateString(),
                'po_description' => 'Procurement electrical goods',
                'billing_address' => 'Headquarters, Colombo',
                'status' => PurchaseOrder::STATUS_RECEIVED,
            ])
            ->assertStatus(200);

        $this->assertEquals(PurchaseOrder::STATUS_RECEIVED, $po->fresh()->status);

        // 5. Received -> Draft (Invalid transition should be rejected with 422)
        $this->actingAs($this->procurement)
            ->putJson("/api/purchase-orders/{$poId}", [
                'po_date' => now()->toDateString(),
                'po_description' => 'Procurement electrical goods',
                'billing_address' => 'Headquarters, Colombo',
                'status' => PurchaseOrder::STATUS_DRAFT,
            ])
            ->assertStatus(422);
    }

    #[Test]
    public function viewer_cannot_create_quotations_or_select_contractor()
    {
        $fixtures = $this->createBaseHierarchy();

        $this->actingAs($this->viewer)
            ->postJson('/api/quotations', [
                'job_id' => $fixtures['job']->id,
                'contractor_id' => $fixtures['contractor1']->id,
                'quotation_amount' => 50000,
                'quotation_date' => now()->toDateString(),
            ])
            ->assertStatus(403);
    }
}
