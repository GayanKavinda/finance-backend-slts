<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupportTicketController extends Controller
{
    /**
     * List the authenticated user's support tickets (newest first).
     */
    public function index(Request $request)
    {
        $tickets = $request->user()
            ->supportTickets()
            ->latest()
            ->paginate(10);

        return response()->json($tickets);
    }

    /**
     * Submit a new support ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category'    => ['required', 'string', Rule::in(SupportTicket::CATEGORIES)],
            'priority'    => ['required', 'string', Rule::in(SupportTicket::PRIORITIES)],
            'subject'     => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:5000'],
        ], [
            'category.in'   => 'Please choose a valid ticket category.',
            'priority.in'   => 'Please choose a valid priority level.',
            'subject.max'   => 'The subject may not be longer than 200 characters.',
            'description.max' => 'The description may not be longer than 5000 characters.',
        ]);

        $ticket = $request->user()->supportTickets()->create([
            'category'    => $validated['category'],
            'priority'    => $validated['priority'],
            'subject'     => $validated['subject'],
            'description' => $validated['description'],
            'status'      => SupportTicket::STATUS_OPEN,
        ]);

        return response()->json([
            'message' => 'Support ticket submitted successfully',
            'ticket'  => $ticket,
        ], 201);
    }
}
