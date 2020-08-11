<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
	protected $table = 'cities';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'city_name','country_id'
    ];
}