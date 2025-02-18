<?php

/**
 * @author Lucas Macedo Torres
 * @date 17/02/2025
 */
declare(strict_types = 1);

namespace App\Filters;

use DeepCopy\Exception\PropertyException;
use Illuminate\Http\Request;

class InvoiceFilter extends Filter
{
    protected array $allowedOperatorsFields = [
        'value'        => ['gt', 'eq', 'lt', 'gte', 'lte', 'ne'],
        'type'         => ['eq', 'ne', 'in'],
        'paid'         => ['eq', 'ne'],
        'payment_date' => ['eq', 'ne', 'lt', 'gte', 'lte', 'ne'],
    ];
}
