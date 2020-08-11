<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
	protected $table = 'medications';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'item_code','title','category_id','price','unit_price','add_commision'
    ];

    public function orderItem()
    {
        return $this->belongsTo('App\OrderItem','id','orderitem_id');
    }

    public function medicationCategory()
    {
        return $this->hasOne('App\MedicationCategory','id','category_id');
    }
}