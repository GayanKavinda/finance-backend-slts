<?php

namespace Database\Seeders;

use App\Models\ChequeTransaction;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class ChequeTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $chequeData = [
            [
                'invoice_number'        => 'INV-2026-0002',
                'payer_name'            => 'Dialog Axiata PLC',
                'payer_account_number'  => '00192837465',
                'payer_bank_name'       => 'Commercial Bank of Ceylon',
                'payer_bank_branch'     => 'Union Place Branch',
                'payer_bank_code'       => '7056-012',
                'cheque_number'         => 'CHQ-881203',
                'cheque_date'           => '2026-03-24',
                'amount'                => 2600000.00,
                'amount_in_words'       => 'Two Million Six Hundred Thousand Rupees Only',
                'payee_name'            => 'SLT Digital Services (Pvt) Ltd',
                'status'                => 'Pending',
            ],
            [
                'invoice_number'        => 'INV-2026-0003',
                'payer_name'            => 'Lanka Electricity Company (LECO)',
                'payer_account_number'  => '1092837461',
                'payer_bank_name'       => 'Sampath Bank PLC',
                'payer_bank_branch'     => 'Colombo Fort Branch',
                'payer_bank_code'       => '7278-001',
                'cheque_number'         => 'CHQ-330192',
                'cheque_date'           => '2026-03-04',
                'amount'                => 8750000.00,
                'amount_in_words'       => 'Eight Million Seven Hundred Fifty Thousand Rupees Only',
                'payee_name'            => 'SLT Digital Services (Pvt) Ltd',
                'status'                => 'Cleared',
            ],
        ];

        $inserted = 0;
        foreach ($chequeData as $data) {
            $invoice = Invoice::where('invoice_number', $data['invoice_number'])->first();
            if (!$invoice) continue;

            unset($data['invoice_number']);

            ChequeTransaction::updateOrCreate(
                ['cheque_number' => $data['cheque_number']],
                array_merge($data, ['invoice_id' => $invoice->id])
            );
            $inserted++;
        }

        $this->command->info("✅ ChequeTransactionSeeder: {$inserted} cheque transactions seeded.");
    }
}
