<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContractorBill;
use App\Models\ProjectJob;
use App\Models\Contractor;
use App\Models\PurchaseOrder;

class ContractorBillSeeder extends Seeder
{
    public function run(): void
    {
        $contractors = Contractor::all();
        $jobs = ProjectJob::all();

        if ($contractors->isEmpty() || $jobs->isEmpty()) {
            return;
        }

        // Seed realistic contractor bills linked to jobs and contractors
        foreach ($jobs->take(5) as $index => $job) {
            $contractor = $contractors[$index % $contractors->count()];
            $po = PurchaseOrder::where('job_id', $job->id)->first();

            ContractorBill::create([
                'job_id' => $job->id,
                'contractor_id' => $contractor->id,
                'po_id' => $po ? $po->id : null,
                'bill_number' => sprintf('CB-2026-%04d', $index + 1),
                'amount' => 45000.00 + ($index * 12500),
                'bill_date' => now()->subDays(10 - $index)->toDateString(),
                'document_path' => null,
                'status' => ['Draft', 'Submitted', 'Verified', 'Approved', 'Paid'][$index % 5],
                'notes' => 'Sample initial milestone contractor billing.',
            ]);
        }
    }
}
