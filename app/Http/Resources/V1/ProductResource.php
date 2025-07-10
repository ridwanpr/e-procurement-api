<?php

namespace App\Http\Resources\V1;

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
            'uuid' => $this->uuid,
            'name' => $this->name,
            'details' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'vendor_uuid' => $this->whenLoaded('vendor', fn() => $this->vendor->uuid),
        ];
    }
}
