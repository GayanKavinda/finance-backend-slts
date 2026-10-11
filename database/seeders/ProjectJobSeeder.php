<?php

namespace Database\Seeders;

use App\Models\Tender;
use App\Models\Contractor;
use App\Models\ProjectJob;
use Illuminate\Database\Seeder;

class ProjectJobSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = [
            // TND-2026-001 – Dialog Fibre Optic
            [
                'tender_number'          => 'TND-2026-001',
                'contractor_name'        => 'TechBuild Solutions (Pvt) Ltd',
                'name'                   => 'FTTH Cable Laying – Colombo Zone A',
                'description'            => 'Underground and aerial fibre optic cable installation for Zones A1–A5 in Colombo. Includes duct installation, cable pulling, and splice enclosure installation.',
                'project_value'          => 8500000.00,
                'contractor_quote_amount'=> 8100000.00,
                'contractor_quote_date'  => '2026-01-25',
                'work_start_date'        => '2026-02-05',
                'work_completion_date'   => '2026-05-31',
                'status'                 => 'In Progress',
                'completed_at'           => null,
            ],
            [
                'tender_number'          => 'TND-2026-001',
                'contractor_name'        => 'DataNet Infrastructure Ltd',
                'name'                   => 'FTTH Splicing & Testing – Colombo Zone B',
                'description'            => 'Fibre splicing, ORL testing, and OTDR measurement across Zone B distribution points. Delivery of as-built drawings.',
                'project_value'          => 5500000.00,
                'contractor_quote_amount'=> 5200000.00,
                'contractor_quote_date'  => '2026-02-01',
                'work_start_date'        => '2026-03-01',
                'work_completion_date'   => '2026-06-30',
                'status'                 => 'In Progress',
                'completed_at'           => null,
            ],
            // TND-2026-003 – LECO Smart Metering
            [
                'tender_number'          => 'TND-2026-003',
                'contractor_name'        => 'Apex Telecom Installations Ltd',
                'name'                   => 'Smart Meter Installation – Colombo South',
                'description'            => 'Installation of 15,000 smart meters in Colombo South district. Includes meter mounting, CT/PT wiring, communication module pairing, and AMI head-end registration.',
                'project_value'          => 18000000.00,
                'contractor_quote_amount'=> 17500000.00,
                'contractor_quote_date'  => '2026-01-08',
                'work_start_date'        => '2026-01-15',
                'work_completion_date'   => '2026-06-30',
                'status'                 => 'In Progress',
                'completed_at'           => null,
            ],
            // TND-2026-004 – CEB SCADA
            [
                'tender_number'          => 'TND-2026-004',
                'contractor_name'        => 'PowerLink Electrical Services',
                'name'                   => 'RTU Replacement – Substations 1–5',
                'description'            => 'Decommissioning of old RTUs and installation of new IEC 61850-compliant RTUs at 5 primary substations. Includes panel wiring, FAT, SAT, and commissioning.',
                'project_value'          => 14000000.00,
                'contractor_quote_amount'=> 13500000.00,
                'contractor_quote_date'  => '2026-03-20',
                'work_start_date'        => '2026-04-10',
                'work_completion_date'   => '2026-08-31',
                'status'                 => 'Pending',
                'completed_at'           => null,
            ],
            // TND-2026-005 – SLT Northern Province Broadband
            [
                'tender_number'          => 'TND-2026-005',
                'contractor_name'        => 'CivTech Builders (Pvt) Ltd',
                'name'                   => 'Fibre Trench & Duct – Jaffna District',
                'description'            => 'Civil works for underground cable routing in Jaffna District. 420 km of trench excavation, HDPE duct laying, manhole installation, and backfilling.',
                'project_value'          => 28000000.00,
                'contractor_quote_amount'=> 27200000.00,
                'contractor_quote_date'  => '2025-12-20',
                'work_start_date'        => '2026-01-05',
                'work_completion_date'   => '2026-09-30',
                'status'                 => 'In Progress',
                'completed_at'           => null,
            ],
            // TND-2026-006 – SLT Data Centre Cooling
            [
                'tender_number'          => 'TND-2026-006',
                'contractor_name'        => 'PowerLink Electrical Services',
                'name'                   => 'In-Row Cooling Unit Installation – DC Hall A',
                'description'            => 'Removal of old CRAC units and installation of 12 in-row cooling units with hot aisle containment in Data Centre Hall A. Includes BMS integration and load testing.',
                'project_value'          => 4800000.00,
                'contractor_quote_amount'=> 4600000.00,
                'contractor_quote_date'  => '2026-04-10',
                'work_start_date'        => '2026-05-05',
                'work_completion_date'   => '2026-07-31',
                'status'                 => 'Pending',
                'completed_at'           => null,
            ],
            // TND-2026-007 – NWS SCADA
            [
                'tender_number'          => 'TND-2026-007',
                'contractor_name'        => 'Sigma Engineering & Construction',
                'name'                   => 'Pump Station Telemetry – Southern Zone',
                'description'            => 'Installation of PLC panels, flow meters, level sensors, and GSM telemetry at 40 pump stations in the Southern Zone. Includes SCADA software configuration.',
                'project_value'          => 12000000.00,
                'contractor_quote_amount'=> 11500000.00,
                'contractor_quote_date'  => '2026-02-05',
                'work_start_date'        => '2026-02-20',
                'work_completion_date'   => '2026-07-31',
                'status'                 => 'In Progress',
                'completed_at'           => null,
            ],
            // TND-2026-008 – Mobitel 5G Pilot
            [
                'tender_number'          => 'TND-2026-008',
                'contractor_name'        => 'SkyNet Tower Contractors',
                'name'                   => '5G gNB Installation – Fort & Pettah Sites',
                'description'            => 'Site preparation, tower strengthening, 5G NR antenna installation, and RF optimisation at Colombo Fort and Pettah telecom sites.',
                'project_value'          => 11000000.00,
                'contractor_quote_amount'=> 10500000.00,
                'contractor_quote_date'  => '2026-05-20',
                'work_start_date'        => '2026-06-05',
                'work_completion_date'   => '2026-10-31',
                'status'                 => 'Pending',
                'completed_at'           => null,
            ],
            // TND-2026-010 – Railways CCTV
            [
                'tender_number'          => 'TND-2026-010',
                'contractor_name'        => 'Apex Telecom Installations Ltd',
                'name'                   => 'CCTV & PIS – Phase 1 (Colombo – Gampaha Stations)',
                'description'            => 'Supply and installation of IP cameras, NVRs, and digital passenger information displays at 10 railway stations on the Colombo–Gampaha line.',
                'project_value'          => 9800000.00,
                'contractor_quote_amount'=> 9500000.00,
                'contractor_quote_date'  => '2026-03-25',
                'work_start_date'        => '2026-04-20',
                'work_completion_date'   => '2026-09-30',
                'status'                 => 'In Progress',
                'completed_at'           => null,
            ],
            // TND-2026-002 – Dialog 4G Tower
            [
                'tender_number'          => 'TND-2026-002',
                'contractor_name'        => 'SkyNet Tower Contractors',
                'name'                   => '4G LTE Antenna Swap – Colombo North Cluster',
                'description'            => 'Antenna and RRU replacement on 10 existing towers in the Colombo North cluster. Includes feeder replacement, tower light inspection, and drive test.',
                'project_value'          => 6000000.00,
                'contractor_quote_amount'=> 5800000.00,
                'contractor_quote_date'  => '2026-03-10',
                'work_start_date'        => '2026-03-20',
                'work_completion_date'   => '2026-07-31',
                'status'                 => 'Awarded',
                'completed_at'           => null,
            ],
        ];

        $inserted = 0;
        foreach ($jobs as $data) {
            $tender = Tender::where('tender_number', $data['tender_number'])->first();
            $contractor = Contractor::where('name', $data['contractor_name'])->first();

            if (!$tender) continue;

            unset($data['tender_number'], $data['contractor_name']);

            ProjectJob::create(array_merge($data, [
                'tender_id'              => $tender->id,
                'customer_id'            => $tender->customer_id,
                'contractor_id'          => $contractor?->id,
                'selected_contractor_id' => $contractor?->id,
            ]));
            $inserted++;
        }

        $this->command->info("✅ ProjectJobSeeder: {$inserted} project jobs seeded with full details.");
    }
}
