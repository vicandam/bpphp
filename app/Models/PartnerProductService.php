<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerProductService extends Model
{
    use HasFactory;

    protected $table = 'partner_products_services';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_partner_id',
        'name',
        'description',
        'price',
        'points_for_redemption',
        'is_voucher',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'points_for_redemption' => 'decimal:2',
        'is_voucher' => 'boolean',
    ];

    /**
     * Get the business partner that owns this product/service.
     */
    public function businessPartner()
    {
        return $this->belongsTo(BusinessPartner::class);
    }
}
