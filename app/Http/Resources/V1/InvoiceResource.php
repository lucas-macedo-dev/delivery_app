<?php

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    private array $types = ['C' => 'CARTAO', 'B' => 'BOLETO', 'P' => 'PIX'];

    public function toArray(Request $request): array
    {
        $paid = $this->paid;
        return [
            'user'         => [
                'name' => $this->user->name,
            ],
            'type'         => $this->types[$this->type],
            'paid'         => $paid ? 'Pago' : 'Não Pago',
            'value'        => 'R$ ' . number_format($this->value, 2, ',', '.'),
            'paymentDate'  => $paid ? Carbon::parse($this->payment_date)->format('d/m/Y H:i:s') : null,
            'paymentSince' => $paid ? Carbon::parse($this->payment_date)->diffForHumans() : null
        ];
    }
}
