<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_num' => $this->order_num,
            'user' => new UserResource($this->whenLoaded('user')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'order_status' => $this->order_status,
            'is_paid' => $this->is_paid,
            'total_price' => $this->total_price,
            'address' => $this->address,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
