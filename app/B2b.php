<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class B2b extends Model
{
	protected $table = 'b2b';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_name', 'discount','delivery_value','note'
    ];
    
    public function lead()
    {
        return $this->belongsTo('App\Lead','id','organization_id');
    }
}