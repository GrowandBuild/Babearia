@extends('layouts.app')

@section('title', 'Perfil e Configurações')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header da Página -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Configurações</h1>
            <p class="text-gray-600">Gerencie seu perfil e as configurações do sistema</p>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4" id="profile-form">
            @csrf
            @method('PATCH')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Card: Perfil -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h1a7 7 0 007-7h-1z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Perfil</h2>
                                <p class="text-sm text-gray-500">Informações pessoais</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                <!-- Card: Segurança -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Segurança</h2>
                                <p class="text-sm text-gray-500">Senha e conta</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            @include('profile.partials.update-password-form')
                            <div class="pt-4 border-t border-gray-200">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Identidade Visual -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Aparência</h2>
                                <p class="text-sm text-gray-500">Logo e banner</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            @include('profile.partials.site-identity-form')
                        </div>
                    </div>
                </div>

                <!-- Card: Configurações da Empresa -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16a2 2 0 002 2zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Configurações da Empresa</h2>
                                <p class="text-sm text-gray-500">Nome e logo da empresa</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            @include('profile.partials.company-settings-form')
                        </div>
                    </div>
                </div>

                <!-- Card: Aparência do Cliente -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Aparência do Cliente</h2>
                                <p class="text-sm text-gray-500">Cores e tema para clientes</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            @include('profile.partials.client-appearance-form')
                        </div>
                    </div>
                </div>

            </div>

            <!-- Barra de Ações Rápidas -->
            <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative z-10">
                <div class="flex items-center space-x-4">
                    <button type="button" onclick="submitProfileForm()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center cursor-pointer">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10a1 1 0 011 1v10a1 1 0 01-1 1H6a1 1 0 01-1 1V11a1 1 0 011-1z"></path>
                        </svg>
                        Salvar Alterações
                    </button>
                    
                    <button type="button" onclick="window.location.href='/'" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Cancelar
                    </button>
                </div>
            </div>
            
            <script>
                function submitProfileForm() {
                    console.log('Salvando formulário...');
                    const form = document.getElementById('profile-form');
                    const formData = new FormData(form);
                    
                    // Adicionar o método PATCH manualmente
                    formData.append('_method', 'PATCH');
                    
                    fetch('{{ route("profile.update") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                        },
                        body: formData
                    }).then(response => {
                        console.log('Resposta:', response);
                        if (response.ok) {
                            window.location.reload();
                        } else {
                            console.error('Erro na resposta:', response.status);
                        }
                    }).catch(error => {
                        console.error('Erro:', error);
                    });
                }
            </script>
        </form>
        
        <div class="flex items-center space-x-4 mt-4">
            <button onclick="window.location.href='/'" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar ao Site
            </button>
            <button onclick="window.location.href='/agendamentos/agenda'" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10a1 1 0 011 1v10a1 1 0 01-1 1H6a1 1 0 01-1 1V11a1 1 0 011-1z"></path>
                </svg>
                Agenda
            </button>
        </div>
        <div class="text-sm text-gray-500">
            Última atualização: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</div>

<style>
.transition-shadow {
    transition: box-shadow 0.2s ease-in-out;
}
</style>
@endsection
