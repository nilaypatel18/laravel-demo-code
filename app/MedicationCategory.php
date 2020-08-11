<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class MedicationCategory extends Model
{
	protected $table = 'medication_categories';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_name', 'wt_commision','is_active'
    ];

    public function medication(){
        return $this->belongsTo('App\Medication');
    }

}