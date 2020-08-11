<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Address extends Model{

	protected $table = 'addresses';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','email','mobile','phone','address_line1','address_line2','city','state','country_id','postal_code'
    ];

    public function order()
    {
        return $this->belongsTo('App\Order','id','address_id');
    }

    public function lead()
    {
        return $this->belongsTo('App\Lead');
    }
}