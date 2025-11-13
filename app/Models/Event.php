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
        // Try to find active campaign event
        $event = self::where('campaign', 1)->first();

        if (!$event) {
            // If no event found, create a dummy one (and save it to DB)
            $event = self::create([
                'campaign' => 1,
                'name' => 'Temporary Event',
                'description' => 'Placeholder event created automatically.',
                'event_date' => now()->toDateString(),
                'event_time' => now()->format('H:i:s'),
                'venue' => 'TBA',
                'ticket_price' => 0,
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
