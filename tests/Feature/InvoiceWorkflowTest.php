<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\PurchaseOrder;
use App\Models\TaxInvoice;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class InvoiceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $procurement;
    protected $finance;
    protected $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        // Run the seeder to set up roles/permissions
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        // Create Users
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->procurement = User::factory()->create();
        $this->procurement->assignRole('Procurement');

        $this->finance = User::factory()->create();
        $this->finance->assignRole('Finance');

        $this->viewer = User::factory()->create();
        $this->viewer->assignRole('Viewer');
    }

    protected function createInvoice($status = Invoice::STATUS_DRAFT)
    {
        $customer = Customer::create([
            'name' => 'Demo Customer ' . rand(100, 999),
            'billing_address' => '123 Fake Street',
            'tax_number' => 'TAX-' . rand(1000, 9999),
            'contact_person' => 'John Doe'
        ]);

        $tender = \App\Models\Tender::create([
            'tender_number' => 'T-001-' . rand(100, 999),
            'customer_id' => $customer->id,
            'awarded_amount' => 50000,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => \App\Models\Tender::STATUS_AWARDED
        ]);

        $po = PurchaseOrder::create([
            'po_number' => 'PO-' . rand(1000, 9999),
            'po_description' => 'Test PO',
            'po_amount' => 5000.00,
            'billing_address' => '123 Fake Street',
            'tender_id' => $tender->id,
            'customer_id' => $customer->id,
            'status' => \App\Models\PurchaseOrder::STATUS_APPROVED
        ]);

        $invoice = Invoice::create([
            'po_id' => $po->id,
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-TEST-' . rand(1000, 9999),
            'invoice_amount' => 1000.00,
            'invoice_date' => now(),
            'status' => $status,
        ]);

        if ($status !== Invoice::STATUS_DRAFT) {
            // Simulate tax generation if not draft (required for submission)
            TaxInvoice::create([
                'invoice_id' => $invoice->id,
                'tax_amount' => 100,
                'tax_invoice_number' => 'TAX-INV-' . rand(1000, 9999),
                'tax_percentage' => 10,
                'total_amount' => 1100,
            ]);
        }

        return $invoice;
    }

    /** @test */
    public function invoice_creation_logs_audit_entry()
    {
        $po = PurchaseOrder::create([
            'po_number' => 'PO-' . rand(1000, 9999),
            'po_description' => 'Test PO',
            'po_amount' => 5000.00,
            'billing_address' => '123 Fake Street',
            'tender_id' => \App\Models\Tender::create([
                'tender_number' => 'T-001-' . rand(100, 999),
                'customer_id' => Customer::create(['name' => 'Cust', 'billing_address' => 'Addr', 'tax_number' => '123', 'contact_person' => 'Person'])->id,
                'awarded_amount' => 50000,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'status' => \App\Models\Tender::STATUS_AWARDED
            ])->id,
            'customer_id' => 1, // Hacky but works if tender created it properly, actually let's use the one from tender
            'status' => \App\Models\PurchaseOrder::STATUS_APPROVED
        ]);
        // Fix up the PO customer_id
        $po->customer_id = $po->tender->customer_id;
        $po->save();

        $data = [
            'po_id' => $po->id,
            'customer_id' => $po->customer_id,
            'invoice_number' => 'INV-NEW-' . rand(1000, 9999),
            'invoice_amount' => 1000.00,
            'invoice_date' => now()->toDateString(),
        ];

        $response = $this->actingAs($this->procurement)
            ->postJson('/api/invoices', $data);

        $response->assertStatus(201);
        $invoiceId = $response->json('id');

        $this->assertDatabaseHas('invoice_status_history', [
            'invoice_id' => $invoiceId,
            'new_status' => Invoice::STATUS_DRAFT,
            'changed_by' => $this->procurement->id,
        ]);
    }

    /** @test */
    public function procurement_can_submit_tax_generated_invoice()
    {
        $invoice = $this->createInvoice(Invoice::STATUS_TAX_GENERATED);

        $response = $this->actingAs($this->procurement)
            ->postJson("/api/invoices/{$invoice->id}/submit-to-finance");

        $response->assertStatus(200);
        $this->assertEquals(Invoice::STATUS_SUBMITTED, $invoice->fresh()->status);
        $this->assertDatabaseHas('invoice_status_history', [
            'invoice_id' => $invoice->id,
            'new_status' => Invoice::STATUS_SUBMITTED,
            'changed_by' => $this->procurement->id,
        ]);
    }

    /** @test */
    public function finance_can_approve_submitted_invoice()
    {
        $invoice = $this->createInvoice(Invoice::STATUS_SUBMITTED);

        $response = $this->actingAs($this->finance)
            ->postJson("/api/invoices/{$invoice->id}/approve");

        $response->assertStatus(200);
        $this->assertEquals(Invoice::STATUS_APPROVED, $invoice->fresh()->status);
        $this->assertDatabaseHas('invoice_status_history', [
            'invoice_id' => $invoice->id,
            'new_status' => Invoice::STATUS_APPROVED,
            'changed_by' => $this->finance->id,
        ]);
    }

    /** @test */
    public function finance_can_reject_submitted_invoice()
    {
        $invoice = $this->createInvoice(Invoice::STATUS_SUBMITTED);
        $reason = "Details mismatch";

        $response = $this->actingAs($this->finance)
            ->postJson("/api/invoices/{$invoice->id}/reject", [
                'rejection_reason' => $reason
            ]);

        $response->assertStatus(200);
        $this->assertEquals(Invoice::STATUS_REJECTED, $invoice->fresh()->status);
        $this->assertEquals($reason, $invoice->fresh()->rejection_reason);
        $this->assertDatabaseHas('invoice_status_history', [
            'invoice_id' => $invoice->id,
            'new_status' => Invoice::STATUS_REJECTED,
            'reason' => $reason,
        ]);
    }

    /** @test */
    public function rejection_clears_approval_data()
    {
        // 1. Approve an invoice
        $invoice = $this->createInvoice(Invoice::STATUS_SUBMITTED);
        $this->actingAs($this->finance)->postJson("/api/invoices/{$invoice->id}/approve");

        $invoice->refresh();
        $this->assertEquals(Invoice::STATUS_APPROVED, $invoice->status);
        $this->assertNotNull($invoice->approved_by);
        $this->assertNotNull($invoice->approved_at);

        // 2. Reject the approved invoice
        $reason = "Found error after approval";
        $this->actingAs($this->finance)
            ->postJson("/api/invoices/{$invoice->id}/reject", [
                'rejection_reason' => $reason
            ])->assertStatus(200);

        $invoice->refresh();

        // 3. Verify status is Rejected AND approval data is cleared
        $this->assertEquals(Invoice::STATUS_REJECTED, $invoice->status);
        $this->assertNull($invoice->approved_by);
        $this->assertNull($invoice->approved_at);
        $this->assertEquals($reason, $invoice->rejection_reason);
    }

    /** @test */
    public function finance_can_mark_approved_invoice_as_paid()
    {
        $invoice = $this->createInvoice(Invoice::STATUS_APPROVED);

        $paymentData = [
            'payment_reference' => 'REF123',
            'payment_method' => 'Bank Transfer',
            'payment_notes' => 'Paid manually',
        ];

        $response = $this->actingAs($this->finance)
            ->postJson("/api/invoices/{$invoice->id}/mark-paid", $paymentData);

        $response->assertStatus(200);
        $this->assertEquals(Invoice::STATUS_PAID, $invoice->fresh()->status);
        $this->assertNotNull($invoice->fresh()->paid_at);
        $this->assertDatabaseHas('invoice_status_history', [
            'invoice_id' => $invoice->id,
            'new_status' => Invoice::STATUS_PAID,
        ]);
    }

    /** @test */
    public function procurement_cannot_approve_invoice()
    {
        $invoice = $this->createInvoice(Invoice::STATUS_SUBMITTED);

        $response = $this->actingAs($this->procurement)
            ->postJson("/api/invoices/{$invoice->id}/approve");

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function cannot_skip_status_flow()
    {
        // Try to pay a draft invoice directly
        $invoice = $this->createInvoice(Invoice::STATUS_DRAFT);

        $response = $this->actingAs($this->finance)
            ->postJson("/api/invoices/{$invoice->id}/mark-paid", [
                'payment_reference' => 'REF123',
                'payment_method' => 'Cash',
            ]);

        // Should fall validation because state transition Draft -> Paid is not allowed
        $response->assertStatus(422);
    }

    /** @test */
    public function audit_trail_is_accessible()
    {
        $invoice = $this->createInvoice(Invoice::STATUS_SUBMITTED);

        // Log an action first
        $this->actingAs($this->finance)->postJson("/api/invoices/{$invoice->id}/approve");

        $response = $this->actingAs($this->finance)
            ->getJson("/api/invoices/{$invoice->id}/audit-trail");

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['old_status', 'new_status', 'changed_by', 'created_at']
            ]);
    }

    /** @test */
    public function analytics_routes_are_protected()
    {
        // Unauthenticated
        $this->getJson('/api/invoices/status-breakdown')->assertStatus(401);
        $this->getJson('/api/invoices/monthly-trend')->assertStatus(401);
        $this->getJson('/api/invoice-summary')->assertStatus(401);

        // Authenticated but unauthorized (if we had a role with no view-invoice)
        // Currently Viewer has view-invoice, so they CAN see it.
        $this->actingAs($this->viewer)
            ->getJson('/api/invoices/status-breakdown')
            ->assertStatus(200);
    }
}
