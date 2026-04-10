<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::withCount('agendamentos')->get();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'instagram' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9._]+$/',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'observacoes' => 'nullable|string',
            'is_package_client' => 'nullable|boolean',
            'package_total_services' => 'required_if:is_package_client,1|nullable|integer|min:1',
            'package_price' => 'required_if:is_package_client,1|nullable|numeric|min:0',
            'package_start_date' => 'required_if:is_package_client,1|nullable|date',
            'package_end_date' => 'nullable|date|after_or_equal:package_start_date',
            'package_observations' => 'nullable|string',
        ]);

        $data = $request->except('avatar');
        
        // Processar campos de pacote
        $data['is_package_client'] = $request->has('is_package_client');
        if (!$data['is_package_client']) {
            $data['package_total_services'] = null;
            $data['package_used_services'] = 0;
            $data['package_price'] = null;
            $data['package_start_date'] = null;
            $data['package_end_date'] = null;
            $data['package_observations'] = null;
        } else {
            $data['package_used_services'] = 0;
        }
        
        // Upload avatar se fornecido
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        Cliente::create($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load('agendamentos.servico', 'agendamentos.profissional', 'agendamentos.pagamentos');
        
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'instagram' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9._]+$/',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'observacoes' => 'nullable|string',
        ]);

        $data = $request->except('avatar');
        
        // Upload novo avatar se fornecido
        if ($request->hasFile('avatar')) {
            // Deletar avatar antigo se existir
            if ($cliente->avatar && Storage::disk('public')->exists($cliente->avatar)) {
                Storage::disk('public')->delete($cliente->avatar);
            }
            
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $cliente->update($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente removido com sucesso!');
    }

    public function createAccess(Cliente $cliente)
    {
        if ($cliente->user_id) {
            return redirect()->route('clientes.index')
                ->with('error', 'Este cliente já possui acesso ao sistema!');
        }

        return view('clientes.create-access', compact('cliente'));
    }

    public function editAccess(Cliente $cliente)
    {
        if (!$cliente->user_id) {
            return redirect()->route('clientes.index')
                ->with('error', 'Este cliente não possui acesso ao sistema!');
        }

        $user = $cliente->user;
        return view('clientes.edit-access', compact('cliente', 'user'));
    }

    public function storeAccess(Request $request, Cliente $cliente)
    {
        if ($cliente->user_id) {
            return redirect()->route('clientes.index')
                ->with('error', 'Este cliente já possui acesso ao sistema!');
        }

        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Create user account
        $user = User::create([
            'name' => $cliente->nome,
            'username' => $request->username,
            'phone' => $cliente->telefone,
            'email' => $cliente->email,
            'password' => Hash::make($request->password),
            'tipo' => 'cliente',
        ]);

        // Link user to client
        $cliente->update(['user_id' => $user->id]);

        return redirect()->route('clientes.index')
            ->with('success', "Acesso criado com sucesso! Usuário: {$request->username}");
    }

    public function updateAccess(Request $request, Cliente $cliente)
    {
        if (!$cliente->user_id) {
            return redirect()->route('clientes.index')
                ->with('error', 'Este cliente não possui acesso ao sistema!');
        }

        $user = $cliente->user;

        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $userData = [
            'username' => $request->username,
        ];

        // Update password only if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('clientes.index')
            ->with('success', "Acesso atualizado com sucesso! Usuário: {$request->username}");
    }

    public function updateAvatar(Request $request, Cliente $cliente)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete old avatar if exists
        if ($cliente->avatar && Storage::disk('public')->exists($cliente->avatar)) {
            Storage::disk('public')->delete($cliente->avatar);
        }

        // Upload new avatar
        $avatarPath = $request->file('avatar')->store('avatars/clientes', 'public');
        $cliente->update(['avatar' => $avatarPath]);

        return response()->json([
            'success' => true,
            'message' => 'Avatar atualizado com sucesso!',
            'avatar_url' => $cliente->avatar_url
        ]);
    }
}

