<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductUserResource extends JsonResource
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
            'price'       => $this->price,
            'sale'        => $this->sale,
            'width'           => $this->width,
            'height'          => $this->height,
            'length'          => $this->length,
            'num_in_stock'    => $this->num_in_stock,
            'brand'       => new BrandResource($this->whenLoaded('brand')),
            'category'    => new CategoryResource($this->whenLoaded('category')),
            'photos'      => ProductPhotoResource::collection($this->whenLoaded('photos')),
        ];
    }
}
