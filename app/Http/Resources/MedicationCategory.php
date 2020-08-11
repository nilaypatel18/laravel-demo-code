<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MedicationCategory extends JsonResource
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
             'category_name' => $this->category_name,
             'wt_commision' => $this->wt_commision,
             'created_at' => $this->created_at->format('d/m/Y'),
             'updated_at' => $this->updated_at->format('d/m/Y'),
         ];
        
    }
}
