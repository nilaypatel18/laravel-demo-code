<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Subscription extends JsonResource
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
            'tier_name' => $this->tier_name,
            'subscription_fees' => $this->subscription_fees,
            'description' => $this->description,
            'delivery_distance_from' => $this->delivery_distance_from,
            'delivery_distance_to' => $this->delivery_distance_to,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
