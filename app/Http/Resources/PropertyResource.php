<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'manager' => $this->manager,
            'is_active' => $this->is_active,
            'code' => $this->code,
            'img' => $this->brand_img,
            'address' => $this->address,
            'phone' => $this->phone,
            'phone_code' => $this->phone_code,
            'cords' => ['lat'=>$this->lat,'lon'=>$this->lon],
            'rooms'=>$this->rooms
        ];
    }
}
