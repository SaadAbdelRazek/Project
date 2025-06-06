<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'image'      => $this->image ? asset('storage/' . $this->image) : null,
            'percentage' => $this->percentage,
            'status'     => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
