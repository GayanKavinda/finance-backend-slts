<?php

namespace Database\Seeders;

use App\Models\Contractor;
use App\Models\ContractorQuotation;
use App\Models\ProjectJob;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContractorQuotationSeeder extends Seeder
{
    public function run(): void
    {
        $procurement = User::where('email', 'procurement@finance.com')->first()
            ?? User::first();

        if (!$procurement) {
            $this->command->warn('⚠️ ContractorQuotationSeeder: no users found, skipped.');
            return;
        }

        // Three competitive bids per job. For awarded jobs the first entry
        // matches the job's selected contractor and quote amount.
        $quotations = [
            // FTTH Cable Laying – Colombo Zone A (awarded to TechBuild)
            [
                'job_name'          => 'FTTH Cable Laying – Colombo Zone A',
                'contractor_name'   => 'TechBuild Solutions (Pvt) Ltd',
                'quotation_amount'  => 8100000.00,
                'quotation_date'    => '2026-01-25',
                'estimated_days'    => 115,
                'status'            => ContractorQuotation::STATUS_SELECTED,
                'work_scope'        => 'Underground and aerial FTTH cable laying for Zones A1–A5 including duct installation, cable pulling, and splice enclosure installation.',
                'notes'             => 'Lowest compliant bid. Preferred vendor with prior SLT Mobitel references.',
            ],
            [
                'job_name'          => 'FTTH Cable Laying – Colombo Zone A',
                'contractor_name'   => 'CivTech Builders (Pvt) Ltd',
                'quotation_amount'  => 8450000.00,
                'quotation_date'    => '2026-01-26',
                'estimated_days'    => 120,
                'status'            => ContractorQuotation::STATUS_REJECTED,
                'work_scope'        => 'Trenching, duct laying and backfilling only; cabling subcontracted.',
                'notes'             => 'Higher price with cable works subcontracted.',
            ],
            [
                'job_name'          => 'FTTH Cable Laying – Colombo Zone A',
                'contractor_name'   => 'GreenPath Civil Works',
                'quotation_amount'  => 8650000.00,
                'quotation_date'    => '2026-01-27',
                'estimated_days'    => 125,
                'status'            => ContractorQuotation::STATUS_REJECTED,
                'work_scope'        => 'Full scope with environmental-compliance trenching method.',
                'notes'             => 'Highest bid; schedule too long for the Q2 deadline.',
            ],
            // FTTH Splicing & Testing – Colombo Zone B (awarded to DataNet)
            [
                'job_name'          => 'FTTH Splicing & Testing – Colombo Zone B',
                'contractor_name'   => 'DataNet Infrastructure Ltd',
                'quotation_amount'  => 5200000.00,
                'quotation_date'    => '2026-02-01',
                'estimated_days'    => 90,
                'status'            => ContractorQuotation::STATUS_SELECTED,
                'work_scope'        => 'Fibre splicing, ORL testing, OTDR measurement and as-built drawings for Zone B.',
                'notes'             => 'Awarded on best technical score for testing deliverables.',
            ],
            [
                'job_name'          => 'FTTH Splicing & Testing – Colombo Zone B',
                'contractor_name'   => 'TechBuild Solutions (Pvt) Ltd',
                'quotation_amount'  => 5380000.00,
                'quotation_date'    => '2026-02-02',
                'estimated_days'    => 95,
                'status'            => ContractorQuotation::STATUS_REJECTED,
                'work_scope'        => 'Same scope with extended OTDR reporting.',
                'notes'             => null,
            ],
            [
                'job_name'          => 'FTTH Splicing & Testing – Colombo Zone B',
                'contractor_name'   => 'Apex Telecom Installations Ltd',
                'quotation_amount'  => 5500000.00,
                'quotation_date'    => '2026-02-03',
                'estimated_days'    => 100,
                'status'            => ContractorQuotation::STATUS_REJECTED,
                'work_scope'        => 'Splicing and testing with drive-test add-on.',
                'notes'             => null,
            ],
            // Smart Meter Installation – Colombo South (awarded to Apex)
            [
                'job_name'          => 'Smart Meter Installation – Colombo South',
                'contractor_name'   => 'Apex Telecom Installations Ltd',
                'quotation_amount'  => 17500000.00,
                'quotation_date'    => '2026-01-08',
                'estimated_days'    => 165,
                'status'            => ContractorQuotation::STATUS_SELECTED,
                'work_scope'        => 'Installation of 15,000 smart meters including mounting, CT/PT wiring, communication module pairing and AMI head-end registration.',
                'notes'             => 'Only bidder with certified AMI head-end integration experience.',
            ],
            [
                'job_name'          => 'Smart Meter Installation – Colombo South',
                'contractor_name'   => 'DataNet Infrastructure Ltd',
                'quotation_amount'  => 17950000.00,
                'quotation_date'    => '2026-01-09',
                'estimated_days'    => 175,
                'status'            => ContractorQuotation::STATUS_REJECTED,
                'work_scope'        => 'Meter installation without AMI head-end integration.',
                'notes'             => 'Head-end integration priced as optional extra.',
            ],
            [
                'job_name'          => 'Smart Meter Installation – Colombo South',
                'contractor_name'   => 'PowerLink Electrical Services',
                'quotation_amount'  => 18200000.00,
                'quotation_date'    => '2026-01-10',
                'estimated_days'    => 180,
                'status'            => ContractorQuotation::STATUS_REJECTED,
                'work_scope'        => 'Electrical installation focus; metering hardware supplied by SLT.',
                'notes'             => null,
            ],
            // RTU Replacement – Substations 1–5 (Pending – bids open)
            [
                'job_name'          => 'RTU Replacement – Substations 1–5',
                'contractor_name'   => 'PowerLink Electrical Services',
                'quotation_amount'  => 13500000.00,
                'quotation_date'    => '2026-03-20',
                'estimated_days'    => 140,
                'status'            => ContractorQuotation::STATUS_SUBMITTED,
                'work_scope'        => 'Decommissioning and replacement of RTUs with IEC 61850-compliant units including panel wiring, FAT, SAT and commissioning.',
                'notes'             => 'Bid under evaluation.',
            ],
            [
                'job_name'          => 'RTU Replacement – Substations 1–5',
                'contractor_name'   => 'Sigma Engineering & Construction',
                'quotation_amount'  => 13850000.00,
                'quotation_date'    => '2026-03-21',
                'estimated_days'    => 150,
                'status'            => ContractorQuotation::STATUS_SUBMITTED,
                'work_scope'        => 'Full RTU replacement scope with extended SAT documentation.',
                'notes'             => 'Bid under evaluation.',
            ],
            [
                'job_name'          => 'RTU Replacement – Substations 1–5',
                'contractor_name'   => 'TechBuild Solutions (Pvt) Ltd',
                'quotation_amount'  => 14100000.00,
                'quotation_date'    => '2026-03-22',
                'estimated_days'    => 145,
                'status'            => ContractorQuotation::STATUS_SUBMITTED,
                'work_scope'        => 'RTU supply and installation with CEB liaison support.',
                'notes'             => 'Bid under evaluation.',
            ],
            // Fibre Trench & Duct – Jaffna District (awarded to CivTech)
            [
                'job_name'          => 'Fibre Trench & Duct – Jaffna District',
                'contractor_name'   => 'CivTech Builders (Pvt) Ltd',
                'quotation_amount'  => 27200000.00,
                'quotation_date'    => '2025-12-20',
                'estimated_days'    => 270,
                'status'            => ContractorQuotation::STATUS_SELECTED,
                'work_scope'        => '420 km trench excavation, HDPE duct laying, manhole installation and backfilling across Jaffna District.',
                'notes'             => 'Awarded on strongest civil works capacity in the Northern Province.',
            ],
            [
                'job_name'          => 'Fibre Trench & Duct – Jaffna District',
                'contractor_name'   => 'GreenPath Civil Works',
                'quotation_amount'  => 27800000.00,
                'quotation_date'    => '2025-12-21',
                'estimated_days'    => 285,
                'status'            => ContractorQuotation::STATUS_REJECTED,
                'work_scope'        => 'Trenching and duct laying with environmental compliance plan.',
                'notes'             => null,
            ],
            [
                'job_name'          => 'Fibre Trench & Duct – Jaffna District',
                'contractor_name'   => 'Sigma Engineering & Construction',
                'quotation_amount'  => 28100000.00,
                'quotation_date'    => '2025-12-22',
                'estimated_days'    => 290,
                'status'            => ContractorQuotation::STATUS_REJECTED,
                'work_scope'        => 'Civil works with limited Northern Province equipment availability.',
                'notes'             => null,
            ],
            // In-Row Cooling Unit Installation – DC Hall A (Pending – bids open)
            [
                'job_name'          => 'In-Row Cooling Unit Installation – DC Hall A',
                'contractor_name'   => 'PowerLink Electrical Services',
                'quotation_amount'  => 4600000.00,
                'quotation_date'    => '2026-04-10',
                'estimated_days'    => 85,
                'status'            => ContractorQuotation::STATUS_SUBMITTED,
                'work_scope'        => 'Removal of CRAC units and installation of 12 in-row cooling units with hot aisle containment, BMS integration and load testing.',
                'notes'             => 'Bid under evaluation.',
            ],
            [
                'job_name'          => 'In-Row Cooling Unit Installation – DC Hall A',
                'contractor_name'   => 'TechBuild Solutions (Pvt) Ltd',
                'quotation_amount'  => 4780000.00,
                'quotation_date'    => '2026-04-11',
                'estimated_days'    => 90,
                'status'            => ContractorQuotation::STATUS_SUBMITTED,
                'work_scope'        => 'Cooling unit installation with third-party BMS integration.',
                'notes'             => 'Bid under evaluation.',
            ],
            [
                'job_name'          => 'In-Row Cooling Unit Installation – DC Hall A',
                'contractor_name'   => 'Sigma Engineering & Construction',
                'quotation_amount'  => 4900000.00,
                'quotation_date'    => '2026-04-12',
                'estimated_days'    => 95,
                'status'            => ContractorQuotation::STATUS_SUBMITTED,
                'work_scope'        => 'Full mechanical and electrical installation with containment.',
                'notes'             => 'Bid under evaluation.',
            ],
            // Pump Station Telemetry – Southern Zone (awarded to Sigma)
            [
                'job_name'          => 'Pump Station Telemetry – Southern Zone',
                'contractor_name'   => 'Sigma Engineering & Construction',
                'quotation_amount'  => 11500000.00,
                'quotation_date'    => '2026-02-05',
                'estimated_days'    => 160,
                'status'            => ContractorQuotation::STATUS_SELECTED,
                'work_scope'        => 'PLC panels, flow meters, level sensors and GSM telemetry at 40 pump stations with SCADA software configuration.',
                'notes'             => 'Awarded on SCADA configuration capability.',
            ],
            [
                'job_name'          => 'Pump Station Telemetry – Southern Zone',
                'contractor_name'   => 'PowerLink Electrical Services',
                'quotation_amount'  => 11900000.00,
                'quotation_date'    => '2026-02-06',
                'estimated_days'    => 170,
                'status'            => ContractorQuotation::STATUS_REJECTED,
                'work_scope'        => 'Panel and sensor installation without SCADA configuration.',
                'notes'             => 'SCADA software excluded from scope.',
            ],
            [
                'job_name'          => 'Pump Station Telemetry – Southern Zone',
                'contractor_name'   => 'TechBuild Solutions (Pvt) Ltd',
                'quotation_amount'  => 12150000.00,
                'quotation_date'    => '2026-02-07',
                'estimated_days'    => 175,
                'status'            => ContractorQuotation::STATUS_REJECTED,
                'work_scope'        => 'Telemetry hardware with subcontracted SCADA integration.',
                'notes'             => null,
            ],
            // 5G gNB Installation – Fort & Pettah Sites (Pending – bids open)
            [
                'job_name'          => '5G gNB Installation – Fort & Pettah Sites',
                'contractor_name'   => 'SkyNet Tower Contractors',
                'quotation_amount'  => 10500000.00,
                'quotation_date'    => '2026-05-20',
                'estimated_days'    => 130,
                'status'            => ContractorQuotation::STATUS_SUBMITTED,
                'work_scope'        => 'Site preparation, tower strengthening, 5G NR antenna installation and RF optimisation at Fort and Pettah sites.',
                'notes'             => 'Bid under evaluation.',
            ],
            [
                'job_name'          => '5G gNB Installation – Fort & Pettah Sites',
                'contractor_name'   => 'Apex Telecom Installations Ltd',
                'quotation_amount'  => 10850000.00,
                'quotation_date'    => '2026-05-21',
                'estimated_days'    => 140,
                'status'            => ContractorQuotation::STATUS_SUBMITTED,
                'work_scope'        => 'Antenna installation with RF optimisation subcontract.',
                'notes'             => 'Bid under evaluation.',
            ],
            [
                'job_name'          => '5G gNB Installation – Fort & Pettah Sites',
                'contractor_name'   => 'TechBuild Solutions (Pvt) Ltd',
                'quotation_amount'  => 11200000.00,
                'quotation_date'    => '2026-05-22',
                'estimated_days'    => 135,
                'status'            => ContractorQuotation::STATUS_SUBMITTED,
                'work_scope'        => 'Full scope with additional structural reinforcement.',
                'notes'             => 'Bid under evaluation.',
            ],
            // CCTV & PIS – Phase 1 (awarded to Apex)
            [
                'job_name'          => 'CCTV & PIS – Phase 1 (Colombo – Gampaha Stations)',
                'contractor_name'   => 'Apex Telecom Installations Ltd',
                'quotation_amount'  => 9500000.00,
                'quotation_date'    => '2026-03-25',
                'estimated_days'    => 155,
                'status'            => ContractorQuotation::STATUS_SELECTED,
                'work_scope'        => 'Supply and installation of IP cameras, NVRs and passenger information displays at 10 stations on the Colombo–Gampaha line.',
                'notes'             => 'Awarded on best value with railway-sector references.',
            ],
            [
                'job_name'          => 'CCTV & PIS – Phase 1 (Colombo – Gampaha Stations)',
                'contractor_name'   => 'DataNet Infrastructure Ltd',
                'quotation_amount'  => 9750000.00,
                'quotation_date'    => '2026-03-26',
                'estimated_days'    => 160,
                'status'            => ContractorQuotation::STATUS_REJECTED,
                'work_scope'        => 'Camera and NVR installation; displays excluded.',
                'notes'             => null,
            ],
            [
                'job_name'          => 'CCTV & PIS – Phase 1 (Colombo – Gampaha Stations)',
                'contractor_name'   => 'SkyNet Tower Contractors',
                'quotation_amount'  => 9900000.00,
                'quotation_date'    => '2026-03-27',
                'estimated_days'    => 165,
                'status'            => ContractorQuotation::STATUS_REJECTED,
                'work_scope'        => 'Full scope with extended display warranty.',
                'notes'             => null,
            ],
            // 4G LTE Antenna Swap – Colombo North Cluster (awarded to SkyNet)
            [
                'job_name'          => '4G LTE Antenna Swap – Colombo North Cluster',
                'contractor_name'   => 'SkyNet Tower Contractors',
                'quotation_amount'  => 5800000.00,
                'quotation_date'    => '2026-03-10',
                'estimated_days'    => 100,
                'status'            => ContractorQuotation::STATUS_SELECTED,
                'work_scope'        => 'Antenna and RRU replacement on 10 towers including feeder replacement, tower light inspection and drive test.',
                'notes'             => 'Lowest bid with tower climbing certification on file.',
            ],
            [
                'job_name'          => '4G LTE Antenna Swap – Colombo North Cluster',
                'contractor_name'   => 'Apex Telecom Installations Ltd',
                'quotation_amount'  => 6050000.00,
                'quotation_date'    => '2026-03-11',
                'estimated_days'    => 105,
                'status'            => ContractorQuotation::STATUS_REJECTED,
                'work_scope'        => 'Antenna swap without drive test included.',
                'notes'             => null,
            ],
            [
                'job_name'          => '4G LTE Antenna Swap – Colombo North Cluster',
                'contractor_name'   => 'TechBuild Solutions (Pvt) Ltd',
                'quotation_amount'  => 6150000.00,
                'quotation_date'    => '2026-03-12',
                'estimated_days'    => 110,
                'status'            => ContractorQuotation::STATUS_REJECTED,
                'work_scope'        => 'Full scope with extended tower light inspection.',
                'notes'             => null,
            ],
        ];

        $inserted = 0;
        $skipped = 0;

        foreach ($quotations as $data) {
            $job = ProjectJob::where('name', $data['job_name'])->first();
            $contractor = Contractor::where('name', $data['contractor_name'])->first();

            if (!$job || !$contractor) {
                $skipped++;
                continue;
            }

            $attributes = collect($data)
                ->except(['job_name', 'contractor_name'])
                ->merge([
                    'job_id'        => $job->id,
                    'contractor_id' => $contractor->id,
                    'entered_by'    => $procurement->id,
                ])
                ->all();

            ContractorQuotation::firstOrCreate(
                [
                    'job_id'        => $job->id,
                    'contractor_id' => $contractor->id,
                ],
                $attributes
            );
            $inserted++;
        }

        $this->command->info("✅ ContractorQuotationSeeder: {$inserted} quotations seeded ({$skipped} skipped).");
    }
}
