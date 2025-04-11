<?php

namespace App\Http\Resources\Delivery;

use Carbon\Carbon;
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
            'name'        => $this->name,
            'description' => $this->description,
            'image'       => $this->image,
            'available'   => $this->available ? 'Disponível' : 'Indisponível',
            'price'       => 'R$ ' . number_format($this->price, 2, ',', '.'),
        ];
    }
}
