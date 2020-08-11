<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'lead_id','opharma_reference','prescription_file','prescription_file_url','order_date','delivery_type','delivery_charge','basket_value','total_cost','last_subscription_order','address_id','order_status','channel','created_by','source','organization_discount','organization_discount_cost'
    ];

    protected $appends = array('status_label');

    public function orderItem()
    {
        return $this->hasMany('App\OrderItem','order_id');
    }

    public function address()
    {
        return $this->hasOne('App\Address','id','address_id');
    }

    public function lead()
    {
        return $this->belongsTo('App\Lead','lead_id');
    }

    public function getStatuslabelAttribute(){
        //Submitted 1,Processing 2,Dispatched 3,Delivered 4,Cancelled 5,Attempted 6,Unreachable 7
        if($this->order_status == 1){
            return "Submitted";
        }
        if($this->order_status == 2){
            return "Processing";
        }
        if($this->order_status == 3){
            return "Dispatched";
        }
        if($this->order_status == 4){
            return "Delivered";
        }
        if($this->order_status == 5){
            return "Cancelled";
        }
        if($this->order_status == 6){
            return "Attempted";
        }
        if($this->order_status == 7){
            return "Unreachable";
        }
       
    }
}