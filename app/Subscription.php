<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tier_name', 'subscription_fees','description','delivery_distance_from','delivery_distance_to','is_active'
    ];
}