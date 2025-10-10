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
            'name'         => $this->name,
            'description'  => $this->description,
            'available'    => $this->available,
            'price'        => $this->price,
            'unit_measure' => $this->unit_measure,
            'stock'        => $this->stock_quantity,
            'created_at'   => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
            'updated_at'   => Carbon::parse($this->updated_at)->format('d/m/Y H:i:s'),
            'category'     => $this->category,
            'icon'         => $this->categories->icon,
            'need_stok'    => $this->categories->need_stock,
            'id'           => $this->id,
            'image_name'   => $this->image_name,
        ];
    }
}
