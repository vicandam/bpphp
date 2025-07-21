<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // All ticket actions require authentication
    }

    /**
     * Display a listing of the user's purchased tickets.
     */
    public function index()
    {
        $tickets = Auth::user()->tickets()->with('event')->get();
        return view('tickets.index', compact('tickets'));
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        // Ensure the authenticated user owns this ticket
        if (Auth::id() !== $ticket->user_id) {
            abort(403, 'Unauthorized action.');
        }
        $ticket->load('event');
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Store a newly purchased ticket in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            // Add validation for payment confirmation here if integrating a gateway
            // 'payment_status' => ['required', 'in:paid'],
        ]);

        $event = Event::findOrFail($request->event_id);
        $user = Auth::user();

        // Simulate payment success (replace with actual payment gateway logic)
        $paymentSuccessful = true; // Placeholder

        if (!$paymentSuccessful) {
            return back()->withErrors(['payment_error' => 'Payment failed. Please try again.']);
        }

        try {
            $ticketCode = Str::uuid(); // Generate a unique ticket code

            // Generate QR code (placeholder)
            // $qrCodeSvg = QrCode::size(200)->generate($ticketCode);
            // $qrPath = 'qrcodes/tickets/' . $ticketCode . '.svg';
            // Storage::put($qrPath, $qrCodeSvg);

            $joyPointsEarned = floor($event->ticket_price / 500) * 10; // 1 joy point for every P500, 1 joy point = Php10

            $ticket = Ticket::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'ticket_code' => $ticketCode,
                'is_redeemed' => false,
                'joy_points_earned' => $joyPointsEarned,
                'purchase_date' => now(),
                // 'qr_code_path' => $qrPath, // Store QR code path if generated
            ]);

            // Update user's BPP Points Balance
            $user->bpp_points_balance += $joyPointsEarned;
            $user->save();

            return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket purchased successfully! Check your email for details and QR code.');
        } catch (\Exception $e) {
            Log::error('Ticket purchase failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to purchase ticket. Please try again.']);
        }
    }

    /**
     * Mark a ticket as redeemed (Admin/Event Staff action).
     */
    public function redeem(Request $request, Ticket $ticket)
    {
        // This method should be protected by admin or event staff middleware
        $this->middleware('admin'); // Example: only admins can redeem

        if ($ticket->is_redeemed) {
            return back()->with('info', 'This ticket has already been redeemed.');
        }

        try {
            $ticket->is_redeemed = true;
            $ticket->save();
            return back()->with('success', 'Ticket ' . $ticket->ticket_code . ' has been redeemed.');
        } catch (\Exception $e) {
            Log::error('Ticket redemption failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to redeem ticket.']);
        }
    }
}
