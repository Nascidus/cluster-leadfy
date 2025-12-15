<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $configUser = Config::get('dashboard.username');
        $configPass = Config::get('dashboard.password');

        if ($request->input('username') === $configUser && $request->input('password') === $configPass) {
            $request->session()->put('dashboard_auth', true);

            return redirect()->intended(route('ga4.dashboard'));
        }

        return back()
            ->withInput($request->only('username'))
            ->withErrors(['auth' => 'Credenciais inválidas.']);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('dashboard_auth');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}


