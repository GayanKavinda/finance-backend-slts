<?php

namespace Tests\Feature;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SupportTicketTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole('Procurement');
    }

    protected function validPayload(): array
    {
        return [
            'category'    => 'Invoices & Payments',
            'priority'    => 'High',
            'subject'     => 'Invoice status stuck after approval',
            'description' => 'The invoice was approved by finance but the status still shows Submitted on the detail page.',
        ];
    }

    #[Test]
    public function authenticated_user_can_list_their_tickets()
    {
        SupportTicket::create([
            'ticket_number' => 'TKT-2026-0001',
            'user_id'       => $this->user->id,
            'category'      => 'Purchase Orders',
            'priority'      => 'Medium',
            'subject'       => 'PO PDF download fails',
            'description'   => 'The download button returns an error page.',
        ]);

        SupportTicket::create([
            'ticket_number' => 'TKT-2026-0002',
            'user_id'       => User::factory()->create()->id,
            'category'      => 'Other',
            'priority'      => 'Low',
            'subject'       => 'Another user ticket',
            'description'   => 'Should not be visible to anyone else.',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/support-tickets');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.ticket_number', 'TKT-2026-0001');
    }

    #[Test]
    public function user_can_submit_a_support_ticket()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/support-tickets', $this->validPayload());

        $response->assertCreated();
        $response->assertJsonPath('message', 'Support ticket submitted successfully');

        $this->assertDatabaseHas('support_tickets', [
            'user_id'  => $this->user->id,
            'category' => 'Invoices & Payments',
            'priority' => 'High',
            'status'   => 'Open',
            'subject'  => 'Invoice status stuck after approval',
        ]);

        $ticket = SupportTicket::first();
        $this->assertStringStartsWith('TKT-' . now()->year . '-', $ticket->ticket_number);
    }

    #[Test]
    public function submitting_a_ticket_validates_the_payload()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/support-tickets', [
                'category'    => 'Not A Category',
                'priority'    => 'Extreme',
                'subject'     => '',
                'description' => '',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['category', 'priority', 'subject', 'description']);
    }

    #[Test]
    public function guests_cannot_access_support_tickets()
    {
        $this->getJson('/api/support-tickets')->assertStatus(401);
        $this->postJson('/api/support-tickets', $this->validPayload())->assertStatus(401);
    }
}
