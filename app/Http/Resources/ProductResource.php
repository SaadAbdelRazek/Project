<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'image'           => $this->image ? asset('storage/' . $this->image) : null,
            'description'     => $this->description,
            'price'           => $this->price,
            'width'           => $this->width,
            'height'          => $this->height,
            'length'          => $this->length,
            'num_in_stock'    => $this->num_in_stock,
            'status'          => $this->status,
            'priority'        => $this->priority,
            'sale'            => $this->sale,
            'category_id'     => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'brand_id'        => $this->brand_id,
            'brand'           => new BrandResource($this->whenLoaded('brand')),
            'category'        => new CategoryResource($this->whenLoaded('category')),
            'subcategory'     => new SubcategoryResource($this->whenLoaded('subcategory')),
        ];
    }
}
