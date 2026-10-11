<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name'            => 'Dialog Axiata PLC',
                'email'           => 'procurement@dialog.lk',
                'phone'           => '+94 11 267 8000',
                'billing_address' => '475, Union Place, Colombo 02, Sri Lanka',
                'tax_number'      => 'TAX-DA-20010023',
                'contact_person'  => 'Dinesh Jayasuriya',
            ],
            [
                'name'            => 'Lanka Electricity Company (LECO)',
                'email'           => 'finance@leco.lk',
                'phone'           => '+94 11 236 3636',
                'billing_address' => 'LECO House, 367, Dr. Colvin R. de Silva Mawatha, Colombo 02',
                'tax_number'      => 'TAX-LECO-19980017',
                'contact_person'  => 'Priya Wickramasinghe',
            ],
            [
                'name'            => 'Ceylon Electricity Board',
                'email'           => 'accounts@ceb.lk',
                'phone'           => '+94 11 232 3261',
                'billing_address' => 'No. 50, Sir Chittampalam A. Gardiner Mawatha, Colombo 02',
                'tax_number'      => 'TAX-CEB-19560004',
                'contact_person'  => 'Gamini Bandara',
            ],
            [
                'name'            => 'Sri Lanka Telecom PLC',
                'email'           => 'billing@slt.lk',
                'phone'           => '+94 11 200 0000',
                'billing_address' => 'Lotus Road, Colombo 01, Sri Lanka',
                'tax_number'      => 'TAX-SLT-19971001',
                'contact_person'  => 'Chathuranga Mendis',
            ],
            [
                'name'            => 'National Water Supply & Drainage Board',
                'email'           => 'procurement@waterboard.lk',
                'phone'           => '+94 11 269 0005',
                'billing_address' => 'Galle Road, Ratmalana, Sri Lanka',
                'tax_number'      => 'TAX-NWS-19750008',
                'contact_person'  => 'Sanduni Ratnayake',
            ],
            [
                'name'            => 'Mobitel (Pvt) Ltd',
                'email'           => 'accounts@mobitel.lk',
                'phone'           => '+94 11 266 6666',
                'billing_address' => 'Lotus Road, Colombo 01, Sri Lanka',
                'tax_number'      => 'TAX-MOB-20030056',
                'contact_person'  => 'Samantha Perera',
            ],
            [
                'name'            => 'Airport & Aviation Services (Sri Lanka)',
                'email'           => 'finance@airport.lk',
                'phone'           => '+94 11 225 2861',
                'billing_address' => 'Bandaranaike International Airport, Katunayake',
                'tax_number'      => 'TAX-AAV-19820031',
                'contact_person'  => 'Anura Liyanage',
            ],
            [
                'name'            => 'Sri Lanka Railways',
                'email'           => 'procurement@railway.gov.lk',
                'phone'           => '+94 11 243 4215',
                'billing_address' => 'Olcott Mawatha, Colombo 10, Sri Lanka',
                'tax_number'      => 'TAX-SLR-18640001',
                'contact_person'  => 'Kumara Rathnayake',
            ],
        ];

        foreach ($customers as $data) {
            Customer::updateOrCreate(['email' => $data['email']], $data);
        }

        $this->command->info('✅ CustomerSeeder: ' . count($customers) . ' customers seeded.');
    }
}
