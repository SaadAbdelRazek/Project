<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubcategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'image'       => $this->image ? asset('storage/' . $this->image) : null,
            'description' => $this->description,
            'priority'    => $this->priority,
            'status'      => $this->status,
            'created_at'  => $this->created_at,
            'category_id' => $this->category_id,
            'category'    => [
                'id'   => $this->category?->id,
                'name' => $this->category?->name,
            ],
        ];
    }
}
