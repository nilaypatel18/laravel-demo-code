<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
	protected $table = 'countries';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_name'
    ];
}