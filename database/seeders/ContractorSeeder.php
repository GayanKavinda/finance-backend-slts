<?php

namespace Database\Seeders;

use App\Models\Contractor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContractorSeeder extends Seeder
{
    public function run(): void
    {
        $contractors = [
            [
                'name'                => 'TechBuild Solutions (Pvt) Ltd',
                'contact_person'      => 'Rajitha Wijesinghe',
                'email'               => 'rajitha@techbuild.lk',
                'phone'               => '+94 77 812 3344',
                'address'             => 'No. 14, Nawala Road, Nugegoda, Sri Lanka',
                'tax_id'              => 'TIN-TB-20150081',
                'contact'             => 'rajitha@techbuild.lk | +94 77 812 3344',
                'bank_name'           => 'Commercial Bank of Ceylon',
                'bank_account_number' => '8002341567',
                'bank_details'        => 'Commercial Bank | Acc: 8002341567 | Branch: Nugegoda',
                'status'              => 'Active',
                'rating'              => 5,
                'notes'               => 'Preferred vendor for fibre optic and network infrastructure projects.',
            ],
            [
                'name'                => 'Sigma Engineering & Construction',
                'contact_person'      => 'Dhanushka Alwis',
                'email'               => 'dhanushka@sigma-eng.lk',
                'phone'               => '+94 71 923 4455',
                'address'             => '22/A, High Level Road, Maharagama, Sri Lanka',
                'tax_id'              => 'TIN-SE-20120045',
                'contact'             => 'dhanushka@sigma-eng.lk | +94 71 923 4455',
                'bank_name'           => 'Sampath Bank PLC',
                'bank_account_number' => '1054879230',
                'bank_details'        => 'Sampath Bank | Acc: 1054879230 | Branch: Maharagama',
                'status'              => 'Active',
                'rating'              => 4,
                'notes'               => 'Civil and structural works specialist. ISO 9001 certified.',
            ],
            [
                'name'                => 'DataNet Infrastructure Ltd',
                'contact_person'      => 'Sanjay Murugesan',
                'email'               => 'sanjay@datanet.lk',
                'phone'               => '+94 76 234 5566',
                'address'             => 'Level 3, BOC Merchant Tower, Colombo 01',
                'tax_id'              => 'TIN-DN-20180110',
                'contact'             => 'sanjay@datanet.lk | +94 76 234 5566',
                'bank_name'           => 'Bank of Ceylon',
                'bank_account_number' => '76543210001',
                'bank_details'        => 'Bank of Ceylon | Acc: 76543210001 | Branch: Colombo Fort',
                'status'              => 'Active',
                'rating'              => 4,
                'notes'               => 'Specialises in data centre cabling and structured cabling solutions.',
            ],
            [
                'name'                => 'PowerLink Electrical Services',
                'contact_person'      => 'Chaminda Senanayake',
                'email'               => 'chaminda@powerlink.lk',
                'phone'               => '+94 72 345 6677',
                'address'             => 'No. 7, Sirimavo Bandaranayake Mawatha, Kandy',
                'tax_id'              => 'TIN-PL-20090033',
                'contact'             => 'chaminda@powerlink.lk | +94 72 345 6677',
                'bank_name'           => 'Hatton National Bank PLC',
                'bank_account_number' => '023410098776',
                'bank_details'        => 'HNB | Acc: 023410098776 | Branch: Kandy City',
                'status'              => 'Active',
                'rating'              => 3,
                'notes'               => 'Electrical installations and UPS solutions for telecom facilities.',
            ],
            [
                'name'                => 'SkyNet Tower Contractors',
                'contact_person'      => 'Lasitha Kumara',
                'email'               => 'lasitha@skynet-towers.lk',
                'phone'               => '+94 70 456 7788',
                'address'             => 'No. 38, Galle Road, Panadura, Sri Lanka',
                'tax_id'              => 'TIN-SN-20110067',
                'contact'             => 'lasitha@skynet-towers.lk | +94 70 456 7788',
                'bank_name'           => 'Nations Trust Bank PLC',
                'bank_account_number' => '109823456701',
                'bank_details'        => 'NTB | Acc: 109823456701 | Branch: Panadura',
                'status'              => 'Active',
                'rating'              => 5,
                'notes'               => 'Tower erection and antenna installation for mobile network rollout.',
            ],
            [
                'name'                => 'CivTech Builders (Pvt) Ltd',
                'contact_person'      => 'Roshan Fernando',
                'email'               => 'roshan@civtech.lk',
                'phone'               => '+94 77 567 8899',
                'address'             => 'No. 11, Baseline Road, Colombo 09',
                'tax_id'              => 'TIN-CT-20170092',
                'contact'             => 'roshan@civtech.lk | +94 77 567 8899',
                'bank_name'           => 'Seylan Bank PLC',
                'bank_account_number' => '0045678901234',
                'bank_details'        => 'Seylan Bank | Acc: 0045678901234 | Branch: Colombo 09',
                'status'              => 'Active',
                'rating'              => 4,
                'notes'               => 'Road works, cable trenching, and civil finishing for telecom projects.',
            ],
            [
                'name'                => 'Apex Telecom Installations Ltd',
                'contact_person'      => 'Pradeep Gunasekara',
                'email'               => 'pradeep@apex-telecom.lk',
                'phone'               => '+94 71 678 9900',
                'address'             => 'No. 5, New Kandy Road, Malabe, Sri Lanka',
                'tax_id'              => 'TIN-AT-20140078',
                'contact'             => 'pradeep@apex-telecom.lk | +94 71 678 9900',
                'bank_name'           => 'Commercial Bank of Ceylon',
                'bank_account_number' => '9001234567890',
                'bank_details'        => 'Commercial Bank | Acc: 9001234567890 | Branch: Malabe',
                'status'              => 'Active',
                'rating'              => 5,
                'notes'               => 'DSLAM, OLT and broadband equipment installation specialists.',
            ],
            [
                'name'                => 'GreenPath Civil Works',
                'contact_person'      => 'Nilmini Jayawardena',
                'email'               => 'nilmini@greenpath.lk',
                'phone'               => '+94 76 789 0011',
                'address'             => '18, Thimbirigasyaya Road, Colombo 05',
                'tax_id'              => 'TIN-GP-20200115',
                'contact'             => 'nilmini@greenpath.lk | +94 76 789 0011',
                'bank_name'           => 'Bank of Ceylon',
                'bank_account_number' => '5500112233445',
                'bank_details'        => 'Bank of Ceylon | Acc: 5500112233445 | Branch: Colombo 05',
                'status'              => 'Active',
                'rating'              => 3,
                'notes'               => 'Environmental-compliant civil contractor for underground cable laying.',
            ],
        ];

        foreach ($contractors as $data) {
            Contractor::updateOrCreate(['email' => $data['email']], $data);
        }

        // DatabaseSeeder runs with WithoutModelEvents, which suppresses the
        // Contractor::booted() created hook that generates contractor_code.
        // Backfill it here so award letters and code-based lookups work.
        $backfilled = 0;
        foreach (Contractor::whereNull('contractor_code')->get() as $contractor) {
            $code = 'CON-' . str_pad($contractor->id, 6, '0', STR_PAD_LEFT);
            DB::table('contractors')->where('id', $contractor->id)->update(['contractor_code' => $code]);
            $backfilled++;
        }

        $this->command->info('✅ ContractorSeeder: ' . count($contractors) . ' contractors seeded with full details' . ($backfilled ? ", {$backfilled} contractor codes backfilled." : '.'));
    }
}
