<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'event_id',
        'ticket_code',
        'is_redeemed',
        'joy_points_earned',
        'purchase_date',
        'virtual_membership_card_qr',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_redeemed' => 'boolean',
        'joy_points_earned' => 'decimal:2',
        'purchase_date' => 'datetime',
    ];

    /**
     * Get the user who purchased the ticket.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event associated with the ticket.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
