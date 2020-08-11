<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class B2b extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company_name' => $this->company_name,
            'discount' => $this->discount,
            'delivery_value' => $this->delivery_value,
            'note' => $this->note,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}