<?php

namespace App\Http\Resources\Delivery;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ifood_id' => $this->ifood_id,
            'ifood_order_number' => $this->ifood_order_number,
            'order_date' => Carbon::parse($this->order_date)->format('Y-m-d'),
            'total_amount_order' => $this->total_amount_order,
            'total_amount_received' => $this->total_amount_received,
            'status' => $this->status,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
