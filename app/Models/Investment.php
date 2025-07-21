<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'investment_amount',
        'number_of_shares',
        'film_project_id',
        'event_id',
        'referred_by_user_id',
        'investment_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'investment_amount' => 'decimal:2',
        'investment_date' => 'datetime',
    ];

    /**
     * Get the user who made the investment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the film project associated with the investment.
     */
    public function filmProject()
    {
        return $this->belongsTo(FilmProject::class);
    }

    /**
     * Get the event associated with the investment.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who referred this investment.
     */
    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by_user_id');
    }
}
