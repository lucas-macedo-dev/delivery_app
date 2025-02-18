<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\MessageBag;

trait HttpResponse
{
    public function response(string $message, string|int $status, array|Model|JsonResource $data = []): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status'  => $status,
            'data'    => $data
        ], $status);
    }

    public function error(
        string $message,
        string|int $status,
        array|MessageBag $errors = [],
        array $data = []
    ): \Illuminate\Http\JsonResponse {
        return response()->json([
            'message' => $message,
            'status'  => $status,
            'errors'  => $errors,
            'data'    => $data
        ], $status);
    }
}
