<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id','medication_id','quantity','unit_price','subtotal'
    ];

    public function medication()
    {
        return $this->hasOne('App\Medication','id','medication_id');
    }

   
}