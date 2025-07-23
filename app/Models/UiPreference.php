<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UiPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sidebar_color',
        'sidenav_type',
        'navbar_fixed',
        'theme_mode',
    ];

    protected $casts = [
        'navbar_fixed' => 'boolean',
    ];
}
