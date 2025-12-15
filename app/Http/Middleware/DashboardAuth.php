<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('dashboard_auth', false)) {
            return redirect()->route('login')->with('error', 'Faça login para acessar o dashboard.');
        }

        return $next($request);
    }
}


