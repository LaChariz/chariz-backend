<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'avatar' => $this->avatar,
            // Eager load billing details only if requested
            'billing_details' => $this->whenLoaded('billingDetails', function () {
                return BillingDetailsResource::collection($this->billingDetails);
            }),
            // Conditionally load carts if include_carts query parameter is present
            'carts' => $this->when($request->has('include_carts'), function () {
                return CartResource::collection($this->carts);
            }),
            // Conditionally load orders if include_orders query parameter is present
            'orders' => $this->when($request->has('include_orders'), function () {
                return OrderResource::collection($this->orders);
            })
        ];
    }
}
