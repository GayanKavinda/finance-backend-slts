<?php

namespace Database\Seeders;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupportTicketSeeder extends Seeder
{
    public function run(): void
    {
        $procurement = User::where('email', 'procurement@finance.com')->first();
        $finance     = User::where('email', 'finance@finance.com')->first();
        $admin       = User::where('email', 'admin@finance.com')->first();

        if (!$procurement || !$finance || !$admin) {
            $this->command?->warn('SupportTicketSeeder: required users not found — skipped.');
            return;
        }

        // NOTE: DatabaseSeeder runs with WithoutModelEvents, so the model's
        // "creating" hook does not fire — ticket numbers are set explicitly.
        $tickets = [
            [
                'ticket_number' => 'TKT-2026-0001',
                'user_id'       => $procurement->id,
                'category'      => 'Tenders & Jobs',
                'priority'      => 'High',
                'subject'       => 'Cannot select a winning bid on job RTU Replacement',
                'description'   => "On job #4 (RTU Replacement – Substations 1–5) I entered 3 contractor quotations. When I click 'Select' on the PowerLink bid, the page shows a spinner and then nothing happens. The job stays Pending and no award letter button appears. Tried a hard refresh and re-login.",
                'status'         => 'Resolved',
                'created_at'     => now()->subDays(9),
                'resolved_at'    => now()->subDays(8),
            ],
            [
                'ticket_number' => 'TKT-2026-0002',
                'user_id'       => $procurement->id,
                'category'      => 'Purchase Orders',
                'priority'      => 'Medium',
                'subject'       => 'PO PDF download fails for PO-2026-0004',
                'description'   => "Clicking the download icon on PO-2026-0004 opens a new tab that returns an error instead of the PDF. Other POs from the same register download fine. Storage link is in place because invoice PDFs work.",
                'status'         => 'In Progress',
                'created_at'     => now()->subDays(3),
            ],
            [
                'ticket_number' => 'TKT-2026-0003',
                'user_id'       => $finance->id,
                'category'      => 'Invoices & Payments',
                'priority'      => 'Critical',
                'subject'       => 'Partial payment amount exceeding approved total on invoice INV-2026-0003',
                'description'   => "While recording a partial payment for milestone 2 on INV-2026-0003 the form accepted an amount larger than the outstanding balance. The invoice now shows Payment Received with figures that do not add up to the approved amount. Needs correction before the month-end bank reconciliation.",
                'status'         => 'Open',
                'created_at'     => now()->subHours(5),
            ],
            [
                'ticket_number' => 'TKT-2026-0004',
                'user_id'       => $finance->id,
                'category'      => 'Contractor Bills',
                'priority'      => 'Medium',
                'subject'       => 'Bill stuck in Submitted status after finance approval',
                'description'   => "Contractor bill CB-2026-0006 shows Submitted even though I approved it yesterday as the Finance user. The contractor has escalated the delay. The status timeline on the bill card shows the approval was recorded.",
                'status'         => 'In Progress',
                'created_at'     => now()->subDays(2),
            ],
            [
                'ticket_number' => 'TKT-2026-0005',
                'user_id'       => $admin->id,
                'category'      => 'Report a Bug',
                'priority'      => 'High',
                'subject'       => 'Dashboard collection efficiency chart misreads currency preference',
                'description'   => "After switching the currency preference to USD in Settings, the dashboard KPI cards update but the collection efficiency trend chart still labels amounts in LKR. Clearing browser cache and signing in again did not help.",
                'status'         => 'Resolved',
                'created_at'     => now()->subDays(14),
                'resolved_at'    => now()->subDays(12),
            ],
            [
                'ticket_number' => 'TKT-2026-0006',
                'user_id'       => $admin->id,
                'category'      => 'Feature Request',
                'priority'      => 'Low',
                'subject'       => 'Add contract expiry reminders for awarded tenders',
                'description'   => "It would help the procurement team to receive an in-app reminder 30 days before an awarded tender's completion date, similar to the payment milestone notifications. Currently this is tracked manually in a spreadsheet.",
                'status'         => 'Open',
                'created_at'     => now()->subDays(6),
            ],
            [
                'ticket_number' => 'TKT-2026-0007',
                'user_id'       => $procurement->id,
                'category'      => 'Notifications',
                'priority'      => 'Low',
                'subject'       => 'Browser alerts not firing for contractor bill updates',
                'description'   => "The in-app notifications arrive, but desktop (browser) alerts do not pop up when a bill is approved. Browser permission for the site is granted and Browser & Desktop Alerts is enabled in Settings → Notifications.",
                'status'         => 'Resolved',
                'created_at'     => now()->subDays(20),
                'resolved_at'    => now()->subDays(19),
            ],
        ];

        foreach ($tickets as $ticket) {
            SupportTicket::updateOrCreate(
                ['ticket_number' => $ticket['ticket_number']],
                $ticket
            );
        }

        $this->command?->info('✅ SupportTicketSeeder: ' . count($tickets) . ' support tickets seeded.');
    }
}
