<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $subtotal = $this->product->price * $this->quantity;

        return [
            'id' => $this->id,
            'product' => $this->product->product_name,
            'product_price' => $this->product->price,
            'quantity' => $this->quantity,
            'subtotal' => $subtotal
        ];
    }
}
