@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Clientes</h2>
                <a href="{{ route('clientes.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    + Novo Cliente
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avatar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Atendimentos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($clientes as $cliente)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="relative group cursor-pointer" onclick="showAvatarModal({{ $cliente->id }}, '{{ $cliente->nome }}', '{{ $cliente->avatar_url ?? '' }}')">
                                        @if($cliente->avatar)
                                            <img src="{{ $cliente->avatar_url }}" alt="{{ $cliente->nome }}" 
                                                 class="h-12 w-12 rounded-full object-cover ring-2 ring-gray-200 group-hover:ring-indigo-500 transition-all">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center group-hover:bg-indigo-100 transition-all">
                                                <span class="text-gray-500 text-sm font-medium">{{ substr($cliente->nome, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 rounded-full transition-all flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white opacity-0 group-hover:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $cliente->nome }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $cliente->telefone }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $cliente->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $cliente->agendamentos_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('clientes.show', $cliente) }}" class="text-blue-600 hover:text-blue-900 mr-3">Ver</a>
                                    <a href="{{ route('clientes.edit', $cliente) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                    @if($cliente->user_id)
                                        <a href="{{ route('clientes.edit-access', $cliente) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Editar Acesso</a>
                                    @else
                                        <a href="{{ route('clientes.create-access', $cliente) }}" class="text-green-600 hover:text-green-900 mr-3">Criar Acesso</a>
                                    @endif
                                    <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Deseja remover?')" class="text-red-600 hover:text-red-900">
                                            Remover
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Nenhum cliente cadastrado
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Avatar Modal -->
<div id="avatarModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Avatar do Cliente</h3>
                <button onclick="closeAvatarModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="text-center mb-4">
                <div id="modalAvatarDisplay" class="w-32 h-32 mx-auto rounded-full object-cover bg-gray-200 flex items-center justify-center">
                    <!-- Avatar will be displayed here -->
                </div>
                <p id="modalClientName" class="mt-2 text-sm font-medium text-gray-900"></p>
            </div>

            <div class="flex gap-3">
                <button onclick="document.getElementById('avatarFile').click()" 
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Trocar Imagem
                </button>
                <button onclick="closeAvatarModal()" 
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
                    Cancelar
                </button>
            </div>

            <form id="avatarForm" class="hidden" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <input type="file" id="avatarFile" name="avatar" accept="image/*" class="hidden" onchange="uploadAvatar(this)">
            </form>
        </div>
    </div>
</div>

<script>
let currentClientId = null;

function showAvatarModal(clientId, clientName, avatarUrl) {
    currentClientId = clientId;
    document.getElementById('modalClientName').textContent = clientName;
    
    const avatarDisplay = document.getElementById('modalAvatarDisplay');
    
    if (avatarUrl && avatarUrl !== '' && avatarUrl !== 'null') {
        avatarDisplay.innerHTML = `<img src="${avatarUrl}" alt="${clientName}" class="w-32 h-32 rounded-full object-cover">`;
    } else {
        avatarDisplay.innerHTML = `<span class="text-4xl text-gray-500">${clientName.charAt(0).toUpperCase()}</span>`;
    }
    
    document.getElementById('avatarModal').classList.remove('hidden');
}

function closeAvatarModal() {
    document.getElementById('avatarModal').classList.add('hidden');
    currentClientId = null;
}

function uploadAvatar(input) {
    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append('avatar', input.files[0]);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch(`/clientes/${currentClientId}/update-avatar`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao atualizar avatar: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao atualizar avatar');
        });
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('avatarModal');
    if (event.target == modal) {
        closeAvatarModal();
    }
}
</script>

@endsection

