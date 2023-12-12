<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'id' => $this-id,
            // 'user_id' => $this->user_id,
            'parking_slot_id' => $this->parking_slot_id,
            'entry_date' => $this->entry_date
        ];
    }
}
