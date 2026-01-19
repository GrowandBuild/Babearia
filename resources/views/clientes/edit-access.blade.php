@extends('layouts.app')

@section('title', 'Editar Acesso - Cliente')

@section('content')
<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6">Editar Acesso do Cliente</h2>

            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h3 class="font-semibold text-lg mb-2">Dados do Cliente</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="font-medium">Nome:</span> {{ $cliente->nome }}
                    </div>
                    <div>
                        <span class="font-medium">Email:</span> {{ $cliente->email ?: 'Não informado' }}
                    </div>
                    <div>
                        <span class="font-medium">Telefone:</span> {{ $cliente->telefone ?: 'Não informado' }}
                    </div>
                    <div>
                        <span class="font-medium">Agendamentos:</span> {{ $cliente->agendamentos_count ?? 0 }}
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <h3 class="font-semibold text-lg mb-2">Dados de Acesso Atuais</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="font-medium">Usuário:</span> {{ $user->username }}
                    </div>
                    <div>
                        <span class="font-medium">Email de Acesso:</span> {{ $user->email ?: 'Não informado' }}
                    </div>
                    <div>
                        <span class="font-medium">Telefone de Acesso:</span> {{ $user->phone ?: 'Não informado' }}
                    </div>
                    <div>
                        <span class="font-medium">Tipo:</span> <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Cliente</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('clientes.update-access', $cliente) }}">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome de Usuário *</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('username')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-500 mt-1">Este é o login que o cliente usa para acessar o sistema</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nova Senha</label>
                        <input type="password" name="password" 
                               placeholder="Deixe em branco para manter a senha atual"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres. Deixe em branco para não alterar.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
                        <input type="password" name="password_confirmation" 
                               placeholder="Confirme a nova senha"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="font-medium text-yellow-800 mb-2">Informações Importantes:</h4>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• O cliente pode acessar usando email, usuário ou telefone</li>
                            <li>• A senha só será alterada se preencher os campos de nova senha</li>
                            <li>• Mantenha os dados de acesso atualizados para segurança</li>
                            <li>• O cliente tem permissões limitadas ao sistema</li>
                        </ul>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                            Atualizar Acesso
                        </button>
                        <a href="{{ route('clientes.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
