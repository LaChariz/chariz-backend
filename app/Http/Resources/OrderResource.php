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
            'user_id' => $this->user_id,
            'billing_details_id' => $this->billing_details_id,
            'payment_method' => $this->payment_method,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'user' => new UserResource($this->user),
            'order_items' => OrderItemResource::collection($this->orderItems),
        ];
    }
}
