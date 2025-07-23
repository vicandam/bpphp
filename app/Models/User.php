<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        //'ghl_api_key',
        //'ghl_location_id',
        'last_login_at',
        'mobile_no',
        'birthday',
        'city_or_province',
        'country',
        'most_favorite_film',
        'most_favorite_song',
        'greatest_dream',
        'referral_code',
        'referred_by_member_id',
        'is_marketing_agent',
        'is_marketing_catalyst',
        'is_angel_investor',
        'is_golden_hearts_awardee',
        'bpp_wallet_balance',
        'bpp_points_balance',
        'virtual_membership_card_qr',
        'membership_type_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birthday' => 'date',
        'is_marketing_agent' => 'boolean',
        'is_marketing_catalyst' => 'boolean',
        'is_angel_investor' => 'boolean',
        'is_golden_hearts_awardee' => 'boolean',
        'bpp_wallet_balance' => 'decimal:2',
        'bpp_points_balance' => 'decimal:2',
    ];

    public function isAdmin(): bool
    {
        return $this->is_admin == 1;
    }

    public function isMarketingAgent(): bool
    {
        return $this->is_marketing_agent == 1;
    }

    public function uiPreference()
    {
        return $this->hasOne(UIPreference::class);
    }

    /**
     * Get the membership type that the user belongs to.
     */
    public function membershipType()
    {
        return $this->belongsTo(MembershipType::class);
    }

    /**
     * Get the user who referred this user.
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by_member_id');
    }

    /**
     * Get the users who were referred by this user.
     */
    public function referredUsers()
    {
        return $this->hasMany(User::class, 'referred_by_member_id');
    }

    /**
     * Get the referrals made by this user.
     */
    public function madeReferrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Get the referrals where this user was referred.
     */
    public function receivedReferral()
    {
        return $this->hasMany(Referral::class, 'referred_member_id');
    }

    /**
     * Get the tickets purchased by the user.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the investments made by the user.
     */
    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    /**
     * Get the business partners referred by the user.
     */
    public function referredBusinessPartners()
    {
        return $this->hasMany(BusinessPartner::class, 'referred_by_user_id');
    }

    /**
     * Get the sponsors referred by the user.
     */
    public function referredSponsors()
    {
        return $this->hasMany(Sponsor::class, 'referred_by_user_id');
    }

    /**
     * Get the donations made by the user.
     */
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Get the payouts for the user.
     */
    public function payouts()
    {
        return $this->hasMany(Payout::class);
    }
}
