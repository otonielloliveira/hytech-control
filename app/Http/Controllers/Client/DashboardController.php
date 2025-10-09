<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();
        return view('client.dashboard.index', compact('client'));
    }

    public function profile()
    {
        $client = Auth::guard('client')->user();
        return view('client.dashboard.profile', compact('client'));
    }

    public function updateProfile(Request $request)
    {
        $client = Auth::guard('client')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:masculino,feminino,outro,nao_informar',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'birth_date', 'gender', 'bio']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($client->avatar) {
                Storage::disk('public')->delete($client->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('clients/avatars', 'public');
        }

        $client->update($data);

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $client = Auth::guard('client')->user();

        if (!Hash::check($request->current_password, $client->password)) {
            return back()->withErrors(['current_password' => 'Senha atual incorreta.']);
        }

        $client->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Senha alterada com sucesso!');
    }

    public function addresses()
    {
        $client = Auth::guard('client')->user();
        $addresses = $client->addresses()->get();
        
        return view('client.dashboard.addresses', compact('client', 'addresses'));
    }

    public function storeAddress(Request $request)
    {
        $client = Auth::guard('client')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:2',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, remove default from others
        if ($request->is_default) {
            $client->addresses()->update(['is_default' => false]);
        }

        $client->addresses()->create($request->all());

        return back()->with('success', 'Endereço adicionado com sucesso!');
    }

    public function updateAddress(Request $request, ClientAddress $address)
    {
        $client = Auth::guard('client')->user();

        // Check if address belongs to the authenticated client
        if ($address->client_id !== $client->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:2',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, remove default from others
        if ($request->is_default) {
            $client->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($request->all());

        return back()->with('success', 'Endereço atualizado com sucesso!');
    }

    public function deleteAddress(ClientAddress $address)
    {
        $client = Auth::guard('client')->user();

        // Check if address belongs to the authenticated client
        if ($address->client_id !== $client->id) {
            abort(403);
        }

        $address->delete();

        return back()->with('success', 'Endereço removido com sucesso!');
    }

    public function preferences()
    {
        $client = Auth::guard('client')->user();
        return view('client.dashboard.preferences', compact('client'));
    }

    public function updatePreferences(Request $request)
    {
        $client = Auth::guard('client')->user();

        $preferences = [
            'newsletter' => $request->boolean('newsletter'),
            'email_notifications' => $request->boolean('email_notifications'),
            'poll_notifications' => $request->boolean('poll_notifications'),
            'petition_updates' => $request->boolean('petition_updates'),
        ];

        $client->update(['preferences' => $preferences]);

        return back()->with('success', 'Preferências atualizadas com sucesso!');
    }
}
