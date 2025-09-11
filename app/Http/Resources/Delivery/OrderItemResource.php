<?php

/**
 * @author Lucas Macedo Torres
 * @date 10/09/2025
 */
declare(strict_types = 1);

namespace App\Http\Resources\Delivery;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'product' => new ProductResource($this->whenLoaded('product')),
            'quantity' => $this->quantity,
            'price' => $this->unit_price,
            'total' => $this->total_price,
        ];
    }

}
