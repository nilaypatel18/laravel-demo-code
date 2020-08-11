<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
	protected $table = 'leads';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','email','mobile_no','phone','address_id','user_subscription_id','leads_status','lead_owner_id','comments','source','status','organization_id'
    ];

    public function address()
    {
        return $this->hasOne('App\Address','id','address_id');
    }
    
    public function b2b()
    {
        return $this->hasOne('App\B2b','id','organization_id');
    }
}