<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApproved
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->is_approved) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Sua conta ainda não foi aprovada. Aguarde a aprovação do administrador.');
        }

        return $next($request);
    }
}
