<?php

/**
 * @author Lucas Macedo Torres
 * @date 17/02/2025
 */
declare(strict_types = 1);

namespace App\Filters;

use DeepCopy\Exception\PropertyException;
use Illuminate\Http\Request;

abstract class Filter
{
    protected array $allowedOperatorsFields = [];

    protected array $translateOperatorsFields = [
        'gt'  => '>',
        'eq'  => '=',
        'lt'  => '<',
        'gte' => '>=',
        'lte' => '<=',
        'ne'  => '!=',
        'in'  => 'in'
    ];

    /**
     * @throws PropertyException
     */
    public function filter(Request $request)
    {
        $where   = [];
        $whereIn = [];
        if (empty($this->allowedOperatorsFields)) {
            throw new PropertyException("Property translateOperatorsFields is not defined");
        }

        foreach ($this->allowedOperatorsFields as $param => $operators) {
            $queryOperator = $request->query($param);
            if ($queryOperator) {
                foreach ($queryOperator as $operator => $value) {
                    if (!in_array($operator, $operators)) {
                        throw new PropertyException("Operator {$operator} not allowed");
                    }

                    if (str_contains($value, '[')) {
                        $whereIn[] = [
                            $param,
                            explode(',', str_replace(['[', ']'], ['', ''], $value)),
                            $value
                        ];
                    } else {
                        $where[] = [
                            $param,
                            $this->translateOperatorsFields[$operator],
                            $value
                        ];
                    }
                }
            }
        }

        if(empty($where) && empty($whereIn)){
            return [];
        }
        return ['where' => $where, 'whereIn' => $whereIn];
    }
}
