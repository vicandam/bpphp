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
        'link',
        'event_date',
        'event_time',
        'venue',
        'is_movie_screening',
        'is_concert',
        'is_seminar_workshop',
        'is_other_event',
        'ticket_price',
        'campaign'
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
        'campaign' => 'boolean',
        'ticket_price' => 'decimal:2',
    ];

    public static function getActiveCampaignEvent(): ?self
    {
        $event = self::where('campaign', 1)->first();

        if (!$event) {
            // Create a default Event instance (not saved to DB)
            $event = new self([
                'name' => 'Alive @ 50',
                'description' => "Event Details:\nDate/Time: November 29, 2025 (Saturday) / 6:00 PM\nVenue: 11th Floor The Exchange Square Exchange Road Corner San Miguel Avenue Ortigas Center Pasig City",
                'event_date' => '2025-11-29',
                'event_time' => '18:00:00',
                'venue' => '11th Floor The Exchange Square Exchange Road Corner San Miguel Avenue Ortigas Center Pasig City',
                'ticket_price' => 1500.00,
            ]);
        }

        return $event;
    }

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
