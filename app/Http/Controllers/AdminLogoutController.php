<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AdminLogoutController extends Controller
{
    public function logout(Request $request)
    {
        // Faz logout apenas do guard web (admin)
        Auth::guard('web')->logout();
        // Não chama session()->invalidate() globalmente
        // Apenas remove os dados do guard web
        $request->session()->forget('login_web_'.Auth::id());
        // Redireciona para a página de login do admin
        return Redirect::to('/admin/login');
    }
}
