<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Tender;
use App\Models\ProjectJob;
use App\Models\Contractor;
use App\Models\ContractorBill;
use App\Models\ContractorBillDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

class ContractorBillWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $procurement;
    protected $finance;
    protected $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->procurement = User::factory()->create();
        $this->procurement->assignRole('Procurement');

        $this->finance = User::factory()->create();
        $this->finance->assignRole('Finance');

        $this->viewer = User::factory()->create();
        $this->viewer->assignRole('Viewer');
    }

    protected function createBillFixtures($jobStatus = ProjectJob::STATUS_IN_PROGRESS)
    {
        $customer = Customer::create([
            'name' => 'Test Customer ' . rand(100, 999),
            'billing_address' => 'Colombo 03',
            'tax_number' => 'TAX-' . rand(1000, 9999),
            'contact_person' => 'Kamal Perera',
        ]);

        $tender = Tender::create([
            'tender_number' => 'TDR-' . rand(1000, 9999),
            'name' => 'Fibre Optic Rollout',
            'customer_id' => $customer->id,
            'awarded_amount' => 100000,
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'status' => Tender::STATUS_OPEN,
        ]);

        $contractor = Contractor::create([
            'name' => 'ABC Telecom Engineering',
            'contact_person' => 'Nimal Silva',
            'email' => 'nimal' . rand(100, 999) . '@abctelecom.lk',
            'phone' => '0771234567',
            'address' => 'Kandy Road, Kelaniya',
            'status' => 'Active',
        ]);

        $job = ProjectJob::create([
            'tender_id' => $tender->id,
            'customer_id' => $customer->id,
            'name' => 'Phase 1 Trenching',
            'project_value' => 75000,
            'status' => $jobStatus,
            'selected_contractor_id' => $contractor->id,
            'contractor_quote_amount' => 45000,
        ]);

        $bill = ContractorBill::create([
            'job_id' => $job->id,
            'contractor_id' => $contractor->id,
            'bill_number' => 'BILL-' . rand(1000, 9999),
            'amount' => 15000,
            'bill_date' => now()->toDateString(),
            'status' => ContractorBill::STATUS_DRAFT,
            'document_path' => '',
        ]);

        return compact('customer', 'tender', 'contractor', 'job', 'bill');
    }

    #[Test]
    public function procurement_can_create_contractor_bill()
    {
        $fixtures = $this->createBillFixtures();
        $job = $fixtures['job'];
        $contractor = $fixtures['contractor'];

        $billNumber = 'BILL-NEW-' . rand(1000, 9999);

        $response = $this->actingAs($this->procurement)
            ->postJson('/api/contractor-bills', [
                'job_id' => $job->id,
                'contractor_id' => $contractor->id,
                'bill_number' => $billNumber,
                'amount' => 12500,
                'bill_date' => now()->toDateString(),
                'notes' => 'Trenching Milestone 1 Completed',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('contractor_bills', [
            'bill_number' => $billNumber,
            'status' => ContractorBill::STATUS_DRAFT,
        ]);
    }

    #[Test]
    public function bill_cannot_be_verified_without_attached_document()
    {
        $fixtures = $this->createBillFixtures();
        $bill = $fixtures['bill'];

        // Attempt verify without document
        $response = $this->actingAs($this->procurement)
            ->postJson("/api/contractor-bills/{$bill->id}/verify");

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'At least one document (e.g., Contractor Bill) is required']);
    }

    #[Test]
    public function procurement_can_upload_document_and_verify_bill()
    {
        Storage::fake('public');

        $fixtures = $this->createBillFixtures();
        $bill = $fixtures['bill'];

        $file = UploadedFile::fake()->create('bill_scanned.pdf', 100, 'application/pdf');

        $uploadResponse = $this->actingAs($this->procurement)
            ->postJson("/api/contractor-bills/{$bill->id}/upload-document", [
                'file' => $file,
                'document_type' => 'Contractor Invoice',
                'description' => 'Signed completion proof',
            ]);

        $uploadResponse->assertStatus(201);
        $this->assertDatabaseHas('contractor_bill_documents', [
            'contractor_bill_id' => $bill->id,
            'document_type' => 'Contractor Invoice',
        ]);

        // Verify the bill
        $verifyResponse = $this->actingAs($this->procurement)
            ->postJson("/api/contractor-bills/{$bill->id}/verify");

        $verifyResponse->assertStatus(200);
        $this->assertEquals(ContractorBill::STATUS_VERIFIED, $bill->fresh()->status);
        $this->assertNotNull($bill->fresh()->verified_at);
        $this->assertEquals($this->procurement->id, $bill->fresh()->verified_by);
    }

    #[Test]
    public function procurement_can_submit_verified_bill_to_finance()
    {
        $fixtures = $this->createBillFixtures();
        $bill = $fixtures['bill'];

        $bill->update(['status' => ContractorBill::STATUS_VERIFIED]);

        $response = $this->actingAs($this->procurement)
            ->postJson("/api/contractor-bills/{$bill->id}/submit");

        $response->assertStatus(200);
        $this->assertEquals(ContractorBill::STATUS_SUBMITTED, $bill->fresh()->status);
        $this->assertEquals($this->procurement->id, $bill->fresh()->submitted_by);
    }

    #[Test]
    public function finance_can_approve_submitted_bill()
    {
        $fixtures = $this->createBillFixtures();
        $bill = $fixtures['bill'];

        $bill->update(['status' => ContractorBill::STATUS_SUBMITTED]);

        $response = $this->actingAs($this->finance)
            ->postJson("/api/contractor-bills/{$bill->id}/approve");

        $response->assertStatus(200);
        $this->assertEquals(ContractorBill::STATUS_APPROVED, $bill->fresh()->status);
        $this->assertEquals($this->finance->id, $bill->fresh()->approved_by);
    }

    #[Test]
    public function finance_can_reject_submitted_bill_with_reason()
    {
        $fixtures = $this->createBillFixtures();
        $bill = $fixtures['bill'];

        $bill->update(['status' => ContractorBill::STATUS_SUBMITTED]);

        $response = $this->actingAs($this->finance)
            ->postJson("/api/contractor-bills/{$bill->id}/reject", [
                'reason' => 'Calculations do not match the quotation rates',
            ]);

        $response->assertStatus(200);
        $this->assertEquals(ContractorBill::STATUS_REJECTED, $bill->fresh()->status);
        $this->assertEquals('Calculations do not match the quotation rates', $bill->fresh()->rejection_reason);
    }

    #[Test]
    public function finance_can_record_payment_and_settle_bill()
    {
        $fixtures = $this->createBillFixtures();
        $bill = $fixtures['bill'];

        $bill->update([
            'status' => ContractorBill::STATUS_APPROVED,
            'amount' => 15000,
        ]);

        $response = $this->actingAs($this->finance)
            ->postJson("/api/contractor-bills/{$bill->id}/pay", [
                'payment_reference' => 'TXN-CHEQUE-7788',
                'bank_name' => 'Bank of Ceylon',
                'payment_amount' => 15000,
                'paid_at' => now()->toDateString(),
                'notes' => 'Settled in full via cheque',
            ]);

        $response->assertStatus(200);
        $this->assertEquals(ContractorBill::STATUS_PAID, $bill->fresh()->status);
        $this->assertEquals(15000, (float)$bill->fresh()->payment_amount);
        $this->assertDatabaseHas('contractor_bill_payments', [
            'contractor_bill_id' => $bill->id,
            'payment_reference' => 'TXN-CHEQUE-7788',
            'amount' => 15000,
        ]);
    }

    #[Test]
    public function unauthorized_roles_cannot_approve_or_pay_bills()
    {
        $fixtures = $this->createBillFixtures();
        $bill = $fixtures['bill'];

        $bill->update(['status' => ContractorBill::STATUS_SUBMITTED]);

        // Procurement cannot approve
        $this->actingAs($this->procurement)
            ->postJson("/api/contractor-bills/{$bill->id}/approve")
            ->assertStatus(403);

        // Viewer cannot approve
        $this->actingAs($this->viewer)
            ->postJson("/api/contractor-bills/{$bill->id}/approve")
            ->assertStatus(403);

        // Viewer cannot record pay
        $this->actingAs($this->viewer)
            ->postJson("/api/contractor-bills/{$bill->id}/pay", [
                'payment_reference' => 'TXN-123',
                'bank_name' => 'Test Bank',
                'payment_amount' => 1000,
                'paid_at' => now()->toDateString(),
            ])
            ->assertStatus(403);
    }
}
