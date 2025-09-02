<?php

namespace App\Services;

use App\Models\User;
use App\Models\TicketRequest;
use App\Models\Investment;

class UserMetricsService
{
    /**
     * Calculates the total revenue contributed by a user from paid ticket requests and investments.
     *
     * @param User $user The user model.
     * @return float The total contributed amount.
     */
    public function calculateRevenueContributed(User $user): float
    {
        // Sum all amounts from paid ticket requests.
        $ticketRevenue = $user->ticketRequests()
            ->where('status', 'paid')
            ->sum('amount');

        // Sum all investment amounts made by the user.
        $investmentRevenue = $user->investments()->sum('investment_amount');

        return (float) ($ticketRevenue + $investmentRevenue);
    }
}
