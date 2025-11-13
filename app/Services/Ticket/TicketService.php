<?php

namespace App\Services\Ticket;

use App\Models\Referral;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Payout;
use App\Services\QrCodeGeneratorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketService
{
    public function __construct(
        private readonly QrCodeGeneratorService $qrCodeGeneratorService
    ) {}

    /**
     * Create a ticket purchase and handle referral bonus + points.
     */
    public function createTicket(User $user, Event $event, string $externalId, float $amount): Ticket
    {
        DB::beginTransaction();

        try {
            $this->processReferralBonus($user);

            $points = $this->calculatePoints($amount);
            $qrCodePath = $this->qrCodeGeneratorService->generateForTicket($externalId, $amount);

            $ticket = Ticket::create([
                'user_id'                    => $user->id,
                'event_id'                   => $event->id,
                'ticket_code'                => $externalId,
                'is_redeemed'                => false,
                'joy_points_earned'          => $points,
                'purchase_date'              => now(),
                'virtual_membership_card_qr' => $qrCodePath,
            ]);

            $user->increment('bpp_points_balance', $points);

            DB::commit();

            Log::info("Ticket created successfully.", [
                'user_id'   => $user->id,
                'event_id'  => $event->id,
                'ticket_id' => $ticket->id,
                'points'    => $points,
            ]);

            return $ticket;

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error("Failed to create ticket: {$e->getMessage()}", [
                'user_id'  => $user->id,
                'event_id' => $event->id,
                'external_id' => $externalId,
                'amount'      => $amount,
            ]);

            throw $e;
        }
    }

    /**
     * Handle referral bonus logic.
     */
    private function processReferralBonus(User $user): void
    {
        if (!$user->referred_by_member_id) {
            return;
        }

        $referrer = User::find($user->referred_by_member_id);

        if (!$referrer) {
            return;
        }

        $bonus = 100.00;

        $referrer->increment('bpp_wallet_balance', $bonus);

        $referral = Referral::where('referrer_id', $referrer->id)
            ->where('referred_member_id', $user->id)
            ->first();

        $referral->amount_earned = $bonus;
        $referral->save();

        Payout::create([
            'user_id'          => $referrer->id,
            'type'             => 'Referral Bonus',
            'amount'           => $bonus,
            'transaction_date' => now(),
        ]);

        Log::info("Referral bonus of ₱{$bonus} awarded.", [
            'referrer_id'   => $referrer->id,
            'referrer_name' => $referrer->name,
            'new_balance'   => $referrer->bpp_wallet_balance,
        ]);
    }

    /**
     * Calculate joy points (1 point per ₱200 spent).
     */
    private function calculatePoints(float $amount): int
    {
        return (int) floor($amount / 500);
    }
}
