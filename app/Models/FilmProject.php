<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilmProject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'link',
        'status',
        'target_fund_amount',
        'total_net_theatrical_ticket_sales',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'target_fund_amount' => 'decimal:2',
        'total_net_theatrical_ticket_sales' => 'decimal:2',
    ];

    /**
     * Get the investments made in this film project.
     */
    public function investments()
    {
        return $this->hasMany(Investment::class);
    }
}
