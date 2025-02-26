<?php

namespace App\Http\Controllers;

use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponse;

    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            return $this->response('Authorized', 200, [
                'token'      => $request->user()->createToken('invoice')->plainTextToken,
                'expires_at' => $request->user()->createToken('invoice', ['invoice-store', 'invoice-update'])->accessToken->expires_at,
            ]);
        } else {
            return $this->response('Unauthorized', 403);
        }
    }

    public function logout()
    {
    }
}
