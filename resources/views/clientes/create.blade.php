@extends('layouts.app')

@section('title', 'Novo Cliente')

@section('content')
<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6">Novo Cliente</h2>

            <form method="POST" action="{{ route('clientes.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome *</label>
                        <input type="text" name="nome" value="{{ old('nome') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('nome')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telefone</label>
                        <input type="text" name="telefone" value="{{ old('telefone') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Instagram</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                @
                            </span>
                            <input type="text" name="instagram" value="{{ old('instagram') }}" placeholder="nomeusuario"
                                   class="flex-1 rounded-none rounded-r-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Opcional - Informe apenas o nome de usuário</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Observações</label>
                        <textarea name="observacoes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('observacoes') }}</textarea>
                    </div>

                    <!-- Seção de Pacotes -->
                    <div class="border-t pt-4">
                        <div class="flex items-center mb-4">
                            <input type="checkbox" id="is_package_client" name="is_package_client" value="1" 
                                   {{ old('is_package_client') ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_package_client" class="ml-2 block text-sm text-gray-900">
                                Cliente comprou pacote de serviços
                            </label>
                        </div>

                        <div id="package_fields" class="{{ old('is_package_client') ? '' : 'hidden' }} space-y-4 bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Total de Serviços *</label>
                                    <input type="number" name="package_total_services" value="{{ old('package_total_services', 4) }}" min="1"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Valor do Pacote (R$) *</label>
                                    <input type="number" name="package_price" value="{{ old('package_price', 80.00) }}" min="0" step="0.01"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Data Início</label>
                                    <input type="date" name="package_start_date" value="{{ old('package_start_date', now()->format('Y-m-d')) }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Data Fim (Opcional)</label>
                                    <input type="date" name="package_end_date" value="{{ old('package_end_date') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Observações do Pacote</label>
                                <textarea name="package_observations" rows="2" placeholder="Ex: Pacote promocional 4 cortes por R$ 80,00"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('package_observations') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Cadastrar
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('is_package_client');
    const packageFields = document.getElementById('package_fields');
    
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            packageFields.classList.remove('hidden');
        } else {
            packageFields.classList.add('hidden');
        }
    });
});
</script>
@endpush

