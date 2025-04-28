<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'title'       => $this->title,
            'image'       => $this->image ? asset('storage/' . $this->image) : null,
            'description' => $this->description,
            'priority'    => $this->priority,
            'status'      => $this->status,
            'created_at'  => $this->created_at,
        ];
    }
}
