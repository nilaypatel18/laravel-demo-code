<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (isset($this->orderitem)) {
            $orderItems = array();
            foreach($this->orderitem as $item){
                $temp = array(
                    'order_item_id'=>$item->id,
                    'medication_id'=>$item->medication_id,
                    'quantity'=>$item->quantity,
                    'unit_price'=>$item->unit_price,
                    'subtotal'=>$item->subtotal,
                );
                array_push($orderItems,$temp);
            }
        }
        $address = array();
        if(isset($this->address)){
            $address = array(
                'id' =>$this->address->id,
                'name'=>$this->address->name,
                'email'=>$this->address->email,
                'mobile'=>$this->address->mobile,
                'phone'=>$this->address->phone,
                'address_line1' => $this->address->address_line1,
                'address_line2' => $this->address->address_line2,
                'city' => $this->address->city,
                'state' => $this->address->state,
                'country_id' => $this->address->country_id,
                'postal_code' => $this->address->postal_code,
                'created_at' => $this->address->created_at->format('d/m/Y'),
                'updated_at' => $this->address->updated_at->format('d/m/Y'),
            );
        }
        return [
            'id' => $this->id,
            'lead_id' => $this->lead_id,
            'opharma_reference' => $this->opharma_reference,
            'source' => $this->source,
            'order_status' => $this->order_status,
            'order_status_label' => $this->status_label,
            'prescription_file' => $this->prescription_file,
            'prescription_file_url' => $this->prescription_file_url,
            'order_date' => $this->order_date,
            'delivery_type' => $this->delivery_type,
            'delivery_charge' => $this->delivery_charge,
            'basket_value' => $this->basket_value,
            'organization_discount' => $this->organization_discount,
            'organization_discount_cost' => $this->organization_discount_cost,
            'total_cost' => $this->total_cost,
            'last_subscription_order' => $this->last_subscription_order,
            'orderItems'=>$orderItems,
            'address'=>$address,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
