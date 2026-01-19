@extends('layouts.app')

@section('title', 'Criar Acesso - Cliente')

@section('content')
<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6">Criar Acesso para Cliente</h2>

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

            <form method="POST" action="{{ route('clientes.store-access', $cliente) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome de Usuário *</label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required
                               placeholder="Digite o nome de usuário"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('username')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-500 mt-1">Este será o login do cliente para acessar o sistema</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Senha *</label>
                        <input type="password" name="password" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirmar Senha *</label>
                        <input type="password" name="password_confirmation" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-medium text-blue-800 mb-2">Informações Importantes:</h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• O cliente poderá acessar o sistema usando email, usuário ou telefone</li>
                            <li>• A senha deve ter no mínimo 8 caracteres</li>
                            <li>• O tipo de acesso será "cliente" com permissões limitadas</li>
                            <li>• O cliente poderá visualizar seus próprios agendamentos</li>
                        </ul>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Criar Acesso
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
