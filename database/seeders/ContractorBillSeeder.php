<?php

namespace Database\Seeders;

use App\Models\Contractor;
use App\Models\ContractorBill;
use App\Models\ProjectJob;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContractorBillSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = User::where('email', 'superadmin@finance.com')->first();
        $admin = User::where('email', 'admin@finance.com')->first();
        $procurement = User::where('email', 'procurement@finance.com')->first();
        $finance = User::where('email', 'finance@finance.com')->first();

        $contractorBills = [
            [
                'job_name'           => 'FTTH Cable Laying – Colombo Zone A',
                'contractor_name'    => 'TechBuild Solutions (Pvt) Ltd',
                'po_number'          => 'PO-2026-0001',
                'bill_number'        => 'CB-2026-0001',
                'amount'             => 2700000.00,
                'bill_date'          => '2026-02-28',
                'status'             => 'Paid',
                'verified_by'        => $procurement?->id,
                'verified_at'        => '2026-03-02 10:15:00',
                'submitted_by'       => $procurement?->id,
                'submitted_at'       => '2026-03-02 14:30:00',
                'approved_by'        => $finance?->id,
                'approved_at'        => '2026-03-04 11:00:00',
                'rejected_by'        => null,
                'rejected_at'        => null,
                'rejection_reason'   => null,
                'paid_at'            => '2026-03-05 16:20:00',
                'payment_reference'  => 'TXN-SLT-CB-88019',
                'bank_name'          => 'Commercial Bank of Ceylon',
                'payment_amount'     => 2700000.00,
                'notes'              => 'Milestone 1: 30% advance on civil trenching completion.',
            ],
            [
                'job_name'           => 'FTTH Cable Laying – Colombo Zone A',
                'contractor_name'    => 'TechBuild Solutions (Pvt) Ltd',
                'po_number'          => 'PO-2026-0001',
                'bill_number'        => 'CB-2026-0002',
                'amount'             => 3240000.00,
                'bill_date'          => '2026-03-25',
                'status'             => 'Approved',
                'verified_by'        => $procurement?->id,
                'verified_at'        => '2026-03-26 09:30:00',
                'submitted_by'       => $procurement?->id,
                'submitted_at'       => '2026-03-26 11:45:00',
                'approved_by'        => $admin?->id,
                'approved_at'        => '2026-03-28 15:10:00',
                'rejected_by'        => null,
                'rejected_at'        => null,
                'rejection_reason'   => null,
                'paid_at'            => null,
                'payment_reference'  => null,
                'bank_name'          => 'Commercial Bank of Ceylon',
                'payment_amount'     => null,
                'notes'              => 'Milestone 2: 40% cable pulling & enclosure termination completed.',
            ],
            [
                'job_name'           => 'FTTH Splicing & Testing – Colombo Zone B',
                'contractor_name'    => 'DataNet Infrastructure Ltd',
                'po_number'          => 'PO-2026-0002',
                'bill_number'        => 'CB-2026-0003',
                'amount'             => 1800000.00,
                'bill_date'          => '2026-03-15',
                'status'             => 'Submitted',
                'verified_by'        => $procurement?->id,
                'verified_at'        => '2026-03-16 14:00:00',
                'submitted_by'       => $procurement?->id,
                'submitted_at'       => '2026-03-17 10:20:00',
                'approved_by'        => null,
                'approved_at'        => null,
                'rejected_by'        => null,
                'rejected_at'        => null,
                'rejection_reason'   => null,
                'paid_at'            => null,
                'payment_reference'  => null,
                'bank_name'          => null,
                'payment_amount'     => null,
                'notes'              => 'Initial batch OTDR testing report attached. Pending Finance approval.',
            ],
            [
                'job_name'           => 'Smart Meter Installation – Colombo South',
                'contractor_name'    => 'Apex Telecom Installations Ltd',
                'po_number'          => 'PO-2026-0003',
                'bill_number'        => 'CB-2026-0004',
                'amount'             => 5250000.00,
                'bill_date'          => '2026-02-15',
                'status'             => 'Paid',
                'verified_by'        => $procurement?->id,
                'verified_at'        => '2026-02-16 11:30:00',
                'submitted_by'       => $procurement?->id,
                'submitted_at'       => '2026-02-17 09:15:00',
                'approved_by'        => $finance?->id,
                'approved_at'        => '2026-02-18 16:45:00',
                'rejected_by'        => null,
                'rejected_at'        => null,
                'rejection_reason'   => null,
                'paid_at'            => '2026-02-20 14:10:00',
                'payment_reference'  => 'TXN-SLT-CB-77401',
                'bank_name'          => 'Commercial Bank of Ceylon',
                'payment_amount'     => 5250000.00,
                'notes'              => 'First tranche 5,000 meters installed and AMI registered successfully.',
            ],
            [
                'job_name'           => 'Fibre Trench & Duct – Jaffna District',
                'contractor_name'    => 'CivTech Builders (Pvt) Ltd',
                'po_number'          => 'PO-2026-0005',
                'bill_number'        => 'CB-2026-0005',
                'amount'             => 8160000.00,
                'bill_date'          => '2026-01-31',
                'status'             => 'Paid',
                'verified_by'        => $procurement?->id,
                'verified_at'        => '2026-02-01 10:00:00',
                'submitted_by'       => $procurement?->id,
                'submitted_at'       => '2026-02-02 12:00:00',
                'approved_by'        => $superadmin?->id,
                'approved_at'        => '2026-02-03 14:00:00',
                'rejected_by'        => null,
                'rejected_at'        => null,
                'rejection_reason'   => null,
                'paid_at'            => '2026-02-05 11:30:00',
                'payment_reference'  => 'TXN-SLT-CB-66299',
                'bank_name'          => 'Seylan Bank PLC',
                'payment_amount'     => 8160000.00,
                'notes'              => 'Phase 1 trenching (150km) signoff by resident engineer.',
            ],
            [
                'job_name'           => 'Pump Station Telemetry – Southern Zone',
                'contractor_name'    => 'Sigma Engineering & Construction',
                'po_number'          => 'PO-2026-0007',
                'bill_number'        => 'CB-2026-0006',
                'amount'             => 3450000.00,
                'bill_date'          => '2026-03-10',
                'status'             => 'Verified',
                'verified_by'        => $procurement?->id,
                'verified_at'        => '2026-03-12 16:15:00',
                'submitted_by'       => null,
                'submitted_at'       => null,
                'approved_by'        => null,
                'approved_at'        => null,
                'rejected_by'        => null,
                'rejected_at'        => null,
                'rejection_reason'   => null,
                'paid_at'            => null,
                'payment_reference'  => null,
                'bank_name'          => null,
                'payment_amount'     => null,
                'notes'              => 'Field instrument inspection passed. Waiting for submission.',
            ],
            [
                'job_name'           => 'CCTV & PIS – Phase 1 (Colombo – Gampaha Stations)',
                'contractor_name'    => 'Apex Telecom Installations Ltd',
                'po_number'          => 'PO-2026-0009',
                'bill_number'        => 'CB-2026-0007',
                'amount'             => 1500000.00,
                'bill_date'          => '2026-04-02',
                'status'             => 'Draft',
                'verified_by'        => null,
                'verified_at'        => null,
                'submitted_by'       => null,
                'submitted_at'       => null,
                'approved_by'        => null,
                'approved_at'        => null,
                'rejected_by'        => null,
                'rejected_at'        => null,
                'rejection_reason'   => null,
                'paid_at'            => null,
                'payment_reference'  => null,
                'bank_name'          => null,
                'payment_amount'     => null,
                'notes'              => 'Draft initial invoice for equipment mobilisation.',
            ],
            [
                'job_name'           => '4G LTE Antenna Swap – Colombo North Cluster',
                'contractor_name'    => 'SkyNet Tower Contractors',
                'po_number'          => 'PO-2026-0010',
                'bill_number'        => 'CB-2026-0008',
                'amount'             => 2300000.00,
                'bill_date'          => '2026-03-29',
                'status'             => 'Rejected',
                'verified_by'        => $procurement?->id,
                'verified_at'        => '2026-03-30 11:00:00',
                'submitted_by'       => $procurement?->id,
                'submitted_at'       => '2026-03-30 14:00:00',
                'approved_by'        => null,
                'approved_at'        => null,
                'rejected_by'        => $finance?->id,
                'rejected_at'        => '2026-03-31 16:30:00',
                'rejection_reason'   => 'Missing safety compliance certificate and calibration report for antenna analyzers.',
                'paid_at'            => null,
                'payment_reference'  => null,
                'bank_name'          => null,
                'payment_amount'     => null,
                'notes'              => 'Resubmission requested after annexing test documents.',
            ],
        ];

        $inserted = 0;
        foreach ($contractorBills as $data) {
            $job = ProjectJob::where('name', $data['job_name'])->first();
            $contractor = Contractor::where('name', $data['contractor_name'])->first();
            $po = PurchaseOrder::where('po_number', $data['po_number'])->first();

            if (!$job || !$contractor) continue;

            unset($data['job_name'], $data['contractor_name'], $data['po_number']);

            ContractorBill::updateOrCreate(
                ['bill_number' => $data['bill_number']],
                array_merge($data, [
                    'job_id'        => $job->id,
                    'contractor_id' => $contractor->id,
                    'po_id'         => $po?->id,
                ])
            );
            $inserted++;
        }

        $this->command->info("✅ ContractorBillSeeder: {$inserted} contractor bills seeded with full workflow states.");
    }
}
