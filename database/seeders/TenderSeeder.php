<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Tender;
use Illuminate\Database\Seeder;

class TenderSeeder extends Seeder
{
    public function run(): void
    {
        // Map customer names to tenders
        $tenderData = [
            'Dialog Axiata PLC' => [
                [
                    'tender_number'  => 'TND-2026-001',
                    'name'           => 'Fibre Optic Network Expansion – Western Province',
                    'description'    => 'Installation of FTTH fibre optic cables across 15 exchanges in the Western Province to expand broadband coverage. Includes civil works, cable laying, splicing, and testing.',
                    'notes'          => 'Priority project. Must be completed before Q3 2026. Environmental clearance obtained.',
                    'awarded_amount' => 18500000.00,
                    'budget'         => 20000000.00,
                    'start_date'     => '2026-02-01',
                    'end_date'       => '2026-08-31',
                    'status'         => 'Awarded',
                ],
                [
                    'tender_number'  => 'TND-2026-002',
                    'name'           => '4G LTE Tower Modernisation – Colombo Metro',
                    'description'    => 'Upgrade of 30 existing 2G/3G towers to 4G LTE capability in the Colombo Metropolitan Area. Includes antenna swap, baseband unit installation, and power system upgrade.',
                    'notes'          => 'RFP issued. Two bidders shortlisted.',
                    'awarded_amount' => 12000000.00,
                    'budget'         => 13500000.00,
                    'start_date'     => '2026-03-15',
                    'end_date'       => '2026-09-30',
                    'status'         => 'Open',
                ],
            ],
            'Lanka Electricity Company (LECO)' => [
                [
                    'tender_number'  => 'TND-2026-003',
                    'name'           => 'Smart Metering Infrastructure Deployment',
                    'description'    => 'Supply and installation of 50,000 smart electricity meters across LECO distribution zones. Includes communication module, AMI head-end system, and customer portal integration.',
                    'notes'          => 'Funded by ADB grant. Procurement aligned to ADB guidelines.',
                    'awarded_amount' => 45000000.00,
                    'budget'         => 47000000.00,
                    'start_date'     => '2026-01-10',
                    'end_date'       => '2027-01-10',
                    'status'         => 'Awarded',
                ],
            ],
            'Ceylon Electricity Board' => [
                [
                    'tender_number'  => 'TND-2026-004',
                    'name'           => 'Substation Automation & SCADA Upgrade',
                    'description'    => 'Design, supply, installation and commissioning of SCADA systems for 10 primary substations. Includes RTU replacement, communication network, and operator training.',
                    'notes'          => 'Part of national grid modernisation initiative.',
                    'awarded_amount' => 32000000.00,
                    'budget'         => 35000000.00,
                    'start_date'     => '2026-04-01',
                    'end_date'       => '2026-12-31',
                    'status'         => 'Awarded',
                ],
            ],
            'Sri Lanka Telecom PLC' => [
                [
                    'tender_number'  => 'TND-2026-005',
                    'name'           => 'National Broadband Infrastructure – Northern Province',
                    'description'    => 'Deployment of high-speed broadband to underserved areas in Jaffna, Kilinochchi, Mullaitivu, and Mannar districts. GPON technology. Approx 8,000 km of fibre.',
                    'notes'          => 'Telecom Regulatory Commission mandated project. Universal Service Obligation fund eligible.',
                    'awarded_amount' => 95000000.00,
                    'budget'         => 100000000.00,
                    'start_date'     => '2026-01-01',
                    'end_date'       => '2027-06-30',
                    'status'         => 'Awarded',
                ],
                [
                    'tender_number'  => 'TND-2026-006',
                    'name'           => 'Data Centre Cooling System Retrofit',
                    'description'    => 'Replacement of legacy cooling systems in Colombo-01 Data Centre with energy-efficient in-row cooling. Includes hot aisle/cold aisle containment and BMS integration.',
                    'notes'          => 'Downtime window: 4 hours max per phase.',
                    'awarded_amount' => 8200000.00,
                    'budget'         => 9000000.00,
                    'start_date'     => '2026-05-01',
                    'end_date'       => '2026-08-31',
                    'status'         => 'Open',
                ],
            ],
            'National Water Supply & Drainage Board' => [
                [
                    'tender_number'  => 'TND-2026-007',
                    'name'           => 'SCADA & Telemetry for Water Distribution Network',
                    'description'    => 'Implementation of real-time monitoring and control for 120 pump stations and 8 treatment plants. Includes field instruments, PLCs, communication network, and central control room.',
                    'notes'          => 'World Bank funded. Strict procurement and reporting requirements apply.',
                    'awarded_amount' => 28500000.00,
                    'budget'         => 30000000.00,
                    'start_date'     => '2026-02-15',
                    'end_date'       => '2026-11-30',
                    'status'         => 'Awarded',
                ],
            ],
            'Mobitel (Pvt) Ltd' => [
                [
                    'tender_number'  => 'TND-2026-008',
                    'name'           => '5G NR Pilot Network – Colombo Urban Core',
                    'description'    => 'Deployment of 5G New Radio pilot sites at 12 strategic locations in Colombo CBD. Includes gNB installation, transport network, and core network integration.',
                    'notes'          => 'Pilot phase. Full rollout tender to be issued Q4 2026.',
                    'awarded_amount' => 22000000.00,
                    'budget'         => 25000000.00,
                    'start_date'     => '2026-06-01',
                    'end_date'       => '2026-12-31',
                    'status'         => 'Awarded',
                ],
            ],
            'Airport & Aviation Services (Sri Lanka)' => [
                [
                    'tender_number'  => 'TND-2026-009',
                    'name'           => 'Airport Passenger Wi-Fi & In-Terminal Network',
                    'description'    => 'Design, supply and installation of passenger-grade Wi-Fi 6 infrastructure across all 4 terminals at BIA. Includes access points, controllers, and ISP connectivity.',
                    'notes'          => 'SLA: 99.99% uptime. Penalty clauses apply.',
                    'awarded_amount' => 15000000.00,
                    'budget'         => 16500000.00,
                    'start_date'     => '2026-03-01',
                    'end_date'       => '2026-09-30',
                    'status'         => 'Open',
                ],
            ],
            'Sri Lanka Railways' => [
                [
                    'tender_number'  => 'TND-2026-010',
                    'name'           => 'Railway Station CCTV & Passenger Information System',
                    'description'    => 'Installation of IP CCTV surveillance and digital passenger information display systems at 35 railway stations island-wide. Includes central monitoring room setup.',
                    'notes'          => 'Ministry of Transport directive. Phased delivery: 10 stations in Phase 1.',
                    'awarded_amount' => 19500000.00,
                    'budget'         => 21000000.00,
                    'start_date'     => '2026-04-15',
                    'end_date'       => '2027-04-15',
                    'status'         => 'Awarded',
                ],
            ],
        ];

        $inserted = 0;
        foreach ($tenderData as $customerName => $tenders) {
            $customer = Customer::where('name', $customerName)->first();
            if (!$customer) continue;

            foreach ($tenders as $data) {
                Tender::updateOrCreate(
                    ['tender_number' => $data['tender_number']],
                    array_merge($data, ['customer_id' => $customer->id])
                );
                $inserted++;
            }
        }

        $this->command->info("✅ TenderSeeder: {$inserted} tenders seeded with full details.");
    }
}
