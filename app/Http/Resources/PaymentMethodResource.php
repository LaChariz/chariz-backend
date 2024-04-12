<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
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
            'card_type' => $this->card_type,
            'card_number' => $this->card_number,
            'expiry' => $this->expiry,
            'cvv' => $this->cvv,
            'card_name' => $this->card_name,
            'status' => $this->status,
        ];
    }
}
