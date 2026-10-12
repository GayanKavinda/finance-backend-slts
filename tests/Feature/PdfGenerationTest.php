<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Tender;
use App\Models\ProjectJob;
use App\Models\Contractor;
use App\Models\PurchaseOrder;
use App\Models\Invoice;
use PHPUnit\Framework\Attributes\Test;

class PdfGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected $financeUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->financeUser = User::factory()->create();
        $this->financeUser->assignRole('Finance');
    }

    protected function createHierarchy()
    {
        $customer = Customer::create([
            'name' => 'Dialog Axiata PLC',
            'billing_address' => 'Union Place, Colombo 02',
            'tax_number' => 'TAX-DIALOG-01',
            'contact_person' => 'Dinesh Weerasinghe',
        ]);

        $tender = Tender::create([
            'tender_number' => 'TDR-TELCO-101',
            'name' => '5G Infrastructure Expansion',
            'customer_id' => $customer->id,
            'awarded_amount' => 750000,
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'status' => Tender::STATUS_OPEN,
        ]);

        $contractor = Contractor::create([
            'name' => 'Metro Towers Lanka',
            'contact_person' => 'Ravi Jayasuriya',
            'email' => 'ravi@metrotowers.lk',
            'phone' => '0712345678',
            'address' => 'Nawala Road, Nugegoda',
            'status' => 'Active',
        ]);

        $job = ProjectJob::create([
            'tender_id' => $tender->id,
            'customer_id' => $customer->id,
            'name' => 'Tower Foundation Rigging',
            'project_value' => 200000,
            'status' => ProjectJob::STATUS_IN_PROGRESS,
            'selected_contractor_id' => $contractor->id,
            'contractor_quote_amount' => 140000,
        ]);

        $po = PurchaseOrder::create([
            'po_number' => 'PO-TELCO-5555',
            'po_date' => now()->toDateString(),
            'po_description' => 'Tower Rigging Parts and Labor',
            'po_amount' => 250000,
            'billing_address' => 'Union Place, Colombo 02',
            'tender_id' => $tender->id,
            'customer_id' => $customer->id,
            'job_id' => $job->id,
            'status' => PurchaseOrder::STATUS_APPROVED,
        ]);

        $invoice = Invoice::create([
            'po_id' => $po->id,
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-TELCO-9001',
            'invoice_amount' => 50000,
            'invoice_date' => now()->toDateString(),
            'status' => Invoice::STATUS_APPROVED,
        ]);

        return compact('customer', 'tender', 'contractor', 'job', 'po', 'invoice');
    }

    #[Test]
    public function can_download_invoice_pdf()
    {
        $fixtures = $this->createHierarchy();
        $invoice = $fixtures['invoice'];

        $response = $this->actingAs($this->financeUser)
            ->get("/api/invoices/{$invoice->id}/pdf");

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('Invoice-' . $invoice->invoice_number, $response->headers->get('content-disposition'));
    }

    #[Test]
    public function can_download_purchase_order_pdf()
    {
        $fixtures = $this->createHierarchy();
        $po = $fixtures['po'];

        $response = $this->actingAs($this->financeUser)
            ->get("/api/purchase-orders/{$po->id}/download-pdf");

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('PO-TELCO-5555', $response->headers->get('content-disposition'));
    }

    #[Test]
    public function can_download_job_award_letter_pdf()
    {
        $fixtures = $this->createHierarchy();
        $job = $fixtures['job'];

        $response = $this->actingAs($this->financeUser)
            ->get("/api/jobs/{$job->id}/award-letter");

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString("Award-Letter-{$job->id}", $response->headers->get('content-disposition'));
    }

    #[Test]
    public function internal_receipt_download_requires_receipt_number()
    {
        $fixtures = $this->createHierarchy();
        $invoice = $fixtures['invoice'];

        // Invoice has no receipt_number yet
        $this->actingAs($this->financeUser)
            ->get("/api/invoices/{$invoice->id}/download-receipt")
            ->assertStatus(404);

        // Assign receipt number and try again
        $invoice->update(['receipt_number' => 'RCP-2026-0099']);

        $response = $this->actingAs($this->financeUser)
            ->get("/api/invoices/{$invoice->id}/download-receipt");

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }
}
