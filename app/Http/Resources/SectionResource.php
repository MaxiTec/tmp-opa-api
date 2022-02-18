<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
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
            'description' => $this->description,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
            'status' => $this->status,
            'is_active' => $this->is_active,
            // only if appear in relationship
            // 'questions' => CriteriaResource::collection($this->whenLoaded('criteria')),
            // 'questions' => CriteriaResource::collection($this->whenLoaded('criteria')),
            'areas' => AreaResource::collection($this->whenLoaded('areas')),
            'count_areas' => count($this->areas),
            // 'count_areas' => count($this->areas->where('is_active',true)->where('status',true)),
            // 'updated_at' => $this->updated_at,
        ];
    }
}
