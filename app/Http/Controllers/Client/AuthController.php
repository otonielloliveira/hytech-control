<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('client.auth.login');
    }

    public function showRegisterForm()
    {
        return view('client.auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $client = Client::where('email', $request->email)->first();

        if (!$client || !Hash::check($request->password, $client->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas não conferem com nossos registros.'],
            ]);
        }

        // Update last login
        $client->update([
            'last_login_at' => now()
        ]);

        Auth::guard('client')->login($client, $request->boolean('remember'));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso!',
                'redirect' => route('client.dashboard')
            ]);
        }

        return redirect()->intended(route('client.dashboard'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:masculino,feminino,outro,nao_informar',
        ]);

        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
        ]);

        Auth::guard('client')->login($client);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Conta criada com sucesso!',
                'redirect' => route('client.dashboard')
            ]);
        }

        return redirect()->route('client.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('client')->logout();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso!',
                'redirect' => route('blog.index')
            ]);
        }

        return redirect()->route('blog.index');
    }
}
