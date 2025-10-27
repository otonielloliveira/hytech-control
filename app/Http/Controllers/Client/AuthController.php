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
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ], [
                'email.required' => 'O e-mail é obrigatório.',
                'email.email' => 'O e-mail deve ser um endereço válido.',
                'password.required' => 'A senha é obrigatória.',
            ]);
        } catch (ValidationException $e) {
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        $client = Client::where('email', $request->email)->first();

        if (!$client || !Hash::check($request->password, $client->password)) {
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'As credenciais fornecidas não conferem com nossos registros.',
                    'errors' => [
                        'email' => ['As credenciais fornecidas não conferem com nossos registros.']
                    ]
                ], 422);
            }
            
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas não conferem com nossos registros.'],
            ]);
        }

        // Update last login
        $client->update([
            'last_login_at' => now()
        ]);

        Auth::guard('client')->login($client, $request->boolean('remember'));

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:clients',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|in:masculino,feminino,outro,nao_informar',
            ], [
                'name.required' => 'O nome é obrigatório.',
                'name.max' => 'O nome não pode ter mais de 255 caracteres.',
                'email.required' => 'O e-mail é obrigatório.',
                'email.email' => 'O e-mail deve ser um endereço válido.',
                'email.unique' => 'Este e-mail já está cadastrado.',
                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
                'password.confirmed' => 'A confirmação da senha não confere.',
                'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
                'birth_date.date' => 'A data de nascimento deve ser uma data válida.',
                'gender.in' => 'O gênero selecionado é inválido.',
            ]);
        } catch (ValidationException $e) {
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
        ]);

        Auth::guard('client')->login($client);

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
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
