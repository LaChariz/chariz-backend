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
            'id' => $this->id,
            'product_name' => $this->product_name,
            'product_image' => $this->product_image,
            'price' => $this->price,
            'cost_price' => $this->cost_price,
            'sales_price' => $this->sales_price,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'sold_items' => $this->sold_items,
            'images' => $this->images,
            'additional_info' => $this->additional_info,
            'sku' => $this->sku,
            'weight' => $this->weight,
            'dimensions' => $this->dimensions,
            'shipping_method' => $this->shipping_method,
            'shipping_cost' => $this->shipping_cost,
            'shipping_time' => $this->shipping_time,
            'location' => $this->location,
            'status' => $this->status,
            'visibility' => $this->visibility,
            'reviews' => ReviewResource::collection($this->reviews),
            'categories' => CategoryResource::collection($this->categories),
            'tags' => TagResource::collection($this->tags),
        ];
    }
}
