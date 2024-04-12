<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillingDetailsResource extends JsonResource
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
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'phone' => $this->phone,
            'email' => $this->email,
            'company_name' => $this->company_name,
            'street_address' => $this->street_address,
            'town_city' => $this->town_city,
            'state' => $this->state,
            'country' => $this->country,
            'zip_code' => $this->zip_code,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->user),
        ];
    }
}
