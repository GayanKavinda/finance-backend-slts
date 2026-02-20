<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChequeTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create cheques for invoices
        \App\Models\Invoice::all()->each(function ($invoice) {
            // Create cheque if method is Cheque or status is Paid (simulating previous payment)
            if ($invoice->payment_method === 'Cheque' || $invoice->status === 'Paid') {
                // Ensure we don't duplicate if already paid via Cheque
                if (\App\Models\ChequeTransaction::where('invoice_id', $invoice->id)->doesntExist()) {
                    \App\Models\ChequeTransaction::factory()->create([
                        'invoice_id' => $invoice->id,
                        'amount' => $invoice->invoice_amount,
                        'status' => $invoice->status === 'Paid' ? 'Cleared' : 'Pending',
                    ]);
                }
            }
        });
    }
}
