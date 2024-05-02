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
            'order_number' => $this->order_number,
            'name' => $this->user && $this->user->name ? $this->user->name : $this->billingDetails->firstname . ' ' . $this->billingDetails->lastname,
            'total_price' => $this->total_price,
            'date' => $this->created_at->toDateTimeString(),
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'order_items' => OrderItemResource::collection($this->orderItems),
            'billing_details' => new BillingDetailsResource($this->billingDetails), 
        ];
    }
}
