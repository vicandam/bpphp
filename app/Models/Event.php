<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'event_date',
        'event_time',
        'venue',
        'is_movie_screening',
        'is_concert',
        'is_seminar_workshop',
        'is_other_event',
        'ticket_price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime', // Cast to datetime to handle time correctly
        'is_movie_screening' => 'boolean',
        'is_concert' => 'boolean',
        'is_seminar_workshop' => 'boolean',
        'is_other_event' => 'boolean',
        'ticket_price' => 'decimal:2',
    ];

    /**
     * Get the tickets for the event.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the investments related to this event.
     */
    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    public function pendingOrders()
    {
        return $this->hasMany(PendingOrder::class);
    }

}
