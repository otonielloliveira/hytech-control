<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Verificar se o usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('filament.admin.auth.login')
                ->with('error', 'Você precisa estar logado para acessar esta área.');
        }

        $user = Auth::user();

        // Verificar se o usuário está ativo
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('filament.admin.auth.login')
                ->with('error', 'Sua conta está inativa. Entre em contato com o administrador.');
        }

        // Verificar se tem a permissão específica
        if (!$user->hasPermission($permission)) {
            abort(403, 'Você não tem permissão para acessar esta funcionalidade.');
        }

        return $next($request);
    }
}
