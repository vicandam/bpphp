<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'donor_name',
        'donation_type',
        'amount',
        'description',
        'donation_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'donation_date' => 'datetime',
    ];

    /**
     * Get the user who made the donation (if registered).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
