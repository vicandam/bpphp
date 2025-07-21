<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPartner extends Model
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
        'contact_person',
        'contact_email',
        'contact_phone',
        'agreement_details',
        'referred_by_user_id',
    ];

    /**
     * Get the products and services offered by this business partner.
     */
    public function productsServices()
    {
        return $this->hasMany(PartnerProductService::class);
    }

    /**
     * Get the user who referred this business partner.
     */
    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by_user_id');
    }
}
