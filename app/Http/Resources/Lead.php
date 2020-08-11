<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Lead extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $address = array();
        if(isset($this->address)){
            $address = array(
                'id' =>$this->address->id,
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

        $source = "";
        if($this->source == 1){
            $source = "Flash";
        }
        if($this->source == 2){
            $source = "OPharma";
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile_no' => $this->mobile_no,
            'phone' => $this->phone,
            'source' => $this->source,
            'source_label' => $source,
            'address_id' => $this->address_id,
            'leads_status' => $this->leads_status,
            'lead_owner_id' => $this->lead_owner_id,
            'comments' => $this->comments,
            'organization_id' => $this->organization_id,
            'status' => $this->status,
            'address'=>$this->address,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
