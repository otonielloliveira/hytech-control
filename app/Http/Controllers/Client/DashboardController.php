<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientAddress;
use App\Models\Order;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();
        
        // Buscar estatísticas para o dashboard
        $enrollments = $client->courseEnrollments()
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        $recentOrders = $client->orders()
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        $stats = [
            'total_courses' => $client->courseEnrollments()->count(),
            'active_courses' => $client->courseEnrollments()->where('status', 'active')->count(),
            'completed_courses' => $client->courseEnrollments()->where('status', 'completed')->count(),
            'total_orders' => $client->orders()->count(),
        ];
        
        return view('client.dashboard.index', compact('client', 'enrollments', 'recentOrders', 'stats'));
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

        $address = $client->addresses()->create($request->all());

        // Se for requisição AJAX, retornar JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Endereço adicionado com sucesso!',
                'address' => $address
            ]);
        }

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

    public function orders()
    {
        $client = Auth::guard('client')->user();
        $orders = \App\Models\Order::where('client_id', $client->id)
            ->with(['items', 'paymentMethod'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('client.dashboard.orders', compact('orders'));
    }

    public function orderDetail(\App\Models\Order $order)
    {
        $client = Auth::guard('client')->user();
        
        // Verificar se o pedido pertence ao cliente logado
        if ($order->client_id !== $client->id) {
            abort(404);
        }
        
        $order->load(['items', 'paymentMethod']);
        
        return view('client.dashboard.order-detail', compact('order'));
    }
}
