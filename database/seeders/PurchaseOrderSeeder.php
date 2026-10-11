<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\ProjectJob;
use App\Models\PurchaseOrder;
use App\Models\Tender;
use App\Models\User;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@finance.com')->first();

        $poData = [
            [
                'job_name'       => 'FTTH Cable Laying – Colombo Zone A',
                'po_number'      => 'PO-2026-0001',
                'po_date'        => '2026-01-28',
                'po_description' => 'Purchase Order for underground and aerial FTTH fibre optic cable installation across Colombo Zone A (A1–A5). Scope includes civil works, duct installation, cable pulling, and splice enclosure installation as per approved BOQ.',
                'po_amount'      => 8100000.00,
                'billing_address'=> '475, Union Place, Colombo 02, Sri Lanka',
                'status'         => 'Approved',
            ],
            [
                'job_name'       => 'FTTH Splicing & Testing – Colombo Zone B',
                'po_number'      => 'PO-2026-0002',
                'po_date'        => '2026-02-05',
                'po_description' => 'Purchase Order for fibre optic splicing, ORL testing, and OTDR measurement across Zone B distribution points. Includes delivery of as-built drawings and test certificates.',
                'po_amount'      => 5200000.00,
                'billing_address'=> '475, Union Place, Colombo 02, Sri Lanka',
                'status'         => 'Sent',
            ],
            [
                'job_name'       => 'Smart Meter Installation – Colombo South',
                'po_number'      => 'PO-2026-0003',
                'po_date'        => '2026-01-10',
                'po_description' => 'Purchase Order for installation of 15,000 smart electricity meters in Colombo South. Includes meter mounting, CT/PT wiring, communication module pairing, and AMI system registration.',
                'po_amount'      => 17500000.00,
                'billing_address'=> 'LECO House, 367, Dr. Colvin R. de Silva Mawatha, Colombo 02',
                'status'         => 'Approved',
            ],
            [
                'job_name'       => 'RTU Replacement – Substations 1–5',
                'po_number'      => 'PO-2026-0004',
                'po_date'        => '2026-03-22',
                'po_description' => 'Purchase Order for decommissioning of legacy RTUs and installation of IEC 61850-compliant RTUs at 5 primary CEB substations. Includes panel wiring, FAT, SAT, and commissioning reports.',
                'po_amount'      => 13500000.00,
                'billing_address'=> 'No. 50, Sir Chittampalam A. Gardiner Mawatha, Colombo 02',
                'status'         => 'Draft',
            ],
            [
                'job_name'       => 'Fibre Trench & Duct – Jaffna District',
                'po_number'      => 'PO-2026-0005',
                'po_date'        => '2025-12-22',
                'po_description' => 'Purchase Order for civil works for underground fibre routing in Jaffna District. 420 km trench excavation, HDPE duct laying, manhole installation, backfilling, and reinstatement as per SLT specifications.',
                'po_amount'      => 27200000.00,
                'billing_address'=> 'Lotus Road, Colombo 01, Sri Lanka',
                'status'         => 'Received',
            ],
            [
                'job_name'       => 'In-Row Cooling Unit Installation – DC Hall A',
                'po_number'      => 'PO-2026-0006',
                'po_date'        => '2026-04-12',
                'po_description' => 'Purchase Order for removal of legacy CRAC units and installation of 12 in-row cooling units with hot aisle/cold aisle containment in Data Centre Hall A. Includes BMS integration and load testing.',
                'po_amount'      => 4600000.00,
                'billing_address'=> 'Lotus Road, Colombo 01, Sri Lanka',
                'status'         => 'Draft',
            ],
            [
                'job_name'       => 'Pump Station Telemetry – Southern Zone',
                'po_number'      => 'PO-2026-0007',
                'po_date'        => '2026-02-08',
                'po_description' => 'Purchase Order for installation of PLC panels, flow meters, level sensors, and GSM telemetry units at 40 pump stations in NWS Southern Zone. Includes SCADA software configuration and commissioning.',
                'po_amount'      => 11500000.00,
                'billing_address'=> 'Galle Road, Ratmalana, Sri Lanka',
                'status'         => 'Approved',
            ],
            [
                'job_name'       => '5G gNB Installation – Fort & Pettah Sites',
                'po_number'      => 'PO-2026-0008',
                'po_date'        => '2026-05-22',
                'po_description' => 'Purchase Order for site preparation, tower strengthening, 5G NR antenna installation, and RF optimisation at Colombo Fort and Pettah telecom sites as per Mobitel 5G pilot specifications.',
                'po_amount'      => 10500000.00,
                'billing_address'=> 'Lotus Road, Colombo 01, Sri Lanka',
                'status'         => 'Draft',
            ],
            [
                'job_name'       => 'CCTV & PIS – Phase 1 (Colombo – Gampaha Stations)',
                'po_number'      => 'PO-2026-0009',
                'po_date'        => '2026-03-28',
                'po_description' => 'Purchase Order for supply and installation of IP CCTV cameras, NVRs, and digital passenger information display systems at 10 railway stations on the Colombo–Gampaha line.',
                'po_amount'      => 9500000.00,
                'billing_address'=> 'Olcott Mawatha, Colombo 10, Sri Lanka',
                'status'         => 'Sent',
            ],
            [
                'job_name'       => '4G LTE Antenna Swap – Colombo North Cluster',
                'po_number'      => 'PO-2026-0010',
                'po_date'        => '2026-03-12',
                'po_description' => 'Purchase Order for antenna and RRU replacement on 10 existing towers in Colombo North cluster. Includes feeder cable replacement, tower lighting inspection, and post-installation drive test.',
                'po_amount'      => 5800000.00,
                'billing_address'=> '475, Union Place, Colombo 02, Sri Lanka',
                'status'         => 'Approved',
            ],
        ];

        $inserted = 0;
        foreach ($poData as $data) {
            $job = ProjectJob::where('name', $data['job_name'])->first();
            if (!$job) continue;

            unset($data['job_name']);

            PurchaseOrder::updateOrCreate(
                ['po_number' => $data['po_number']],
                array_merge($data, [
                    'job_id'      => $job->id,
                    'tender_id'   => $job->tender_id,
                    'customer_id' => $job->customer_id,
                ])
            );
            $inserted++;
        }

        // Seed some PO status history for Approved POs
        if ($admin) {
            PurchaseOrder::where('status', 'Approved')->each(function ($po) use ($admin) {
                \DB::table('purchase_order_status_history')->insert([
                    'purchase_order_id' => $po->id,
                    'old_status'        => 'Draft',
                    'new_status'        => 'Approved',
                    'changed_by'        => $admin->id,
                    'reason'            => 'Purchase order reviewed and approved by Finance Administrator.',
                    'metadata'          => json_encode(['source' => 'seeder']),
                    'created_at'        => now(),
                ]);
            });
        }

        $this->command->info("✅ PurchaseOrderSeeder: {$inserted} POs seeded with full details + status history.");
    }
}
