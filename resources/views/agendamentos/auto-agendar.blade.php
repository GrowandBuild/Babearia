@extends('layouts.cliente')

@section('title', 'Agendar Atendimento')

@php $darkMode = \App\Models\Setting::get('site.dark_mode'); @endphp

<!-- Banner Principal - Fora do main para aparecer logo abaixo do header -->
@section('banner')
@php
    $bannerPath = \App\Models\Setting::get('site.banner');
    $bannerUrl = \App\Models\Setting::get('site.banner_url');
@endphp

<div style="width: 100%; height: 250px; position: relative; overflow: hidden; background: linear-gradient(45deg, #667eea, #764ba2);">
    
    @if($bannerUrl)
        <!-- Banner via URL externa -->
        <img src="{{ $bannerUrl }}" 
             alt="Banner" 
             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; display: block;">
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, rgba(0,0,0,0.4), rgba(0,0,0,0.6));"></div>
    @elseif($bannerPath)
        <!-- Banner com imagem personalizada -->
        <img src="{{ asset('storage/' . $bannerPath) }}" 
             alt="Banner" 
             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; display: block;">
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, rgba(0,0,0,0.4), rgba(0,0,0,0.6));"></div>
    @endif
    
    <div class="absolute inset-0 flex items-center justify-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
            <div class="text-center">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3 animate-fade-in drop-shadow-lg">
                    Agende seu Atendimento
                </h1>
                <p class="text-base sm:text-lg text-white/95 max-w-2xl mx-auto animate-fade-in-delay drop-shadow-md">
                    Escolha seus serviços preferidos e agende um horário conveniente. 
                    Atendimento profissional e personalizado para você.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Elementos decorativos -->
    <div class="absolute top-4 right-4 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
    <div class="absolute bottom-4 left-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
    <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-white/5 rounded-full blur-lg"></div>
</div>
@endsection

<!-- Conteúdo Principal -->
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulário (2/3 em desktop) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg ">
@if($darkMode)
<style>
/* Tema Escuro */
body {
    background-color: #000000 !important;
    color: #ffffff !important;
}

.max-w-7xl {
    background-color: #000000 !important;
}

.site-header {
    background: linear-gradient(90deg, #1a1a1a, #2d2d2d) !important;
}

.bg-brand-surface {
    background-color: #1a1a1a !important;
    border-color: #333333 !important;
}

.servico-tile {
    background-color: #2d2d2d !important;
    border-color: #404040 !important;
    color: #ffffff !important;
}

.servico-tile:hover {
    border-color: #3B82F6 !important;
    background-color: #1e40af !important;
}

.servico-tile.selected,
.servico-tile input:checked + .servico-tile {
    border-color: #3B82F6 !important;
    background-color: #1e40af !important;
}

.data-tile {
    background-color: #2d2d2d !important;
    border-color: #404040 !important;
    color: #ffffff !important;
}

.data-tile:hover {
    border-color: #3B82F6 !important;
    background-color: #1e40af !important;
}

.data-tile.selected {
    background-color: #3B82F6 !important;
    color: white !important;
    border-color: #3B82F6 !important;
}

.horario-tile {
    background-color: #2d2d2d !important;
    border-color: #404040 !important;
    color: #ffffff !important;
}

.horario-tile:hover:not(.disabled) {
    border-color: #3B82F6 !important;
    background-color: #1e40af !important;
}

.horario-tile.selected {
    background-color: #3B82F6 !important;
    color: white !important;
    border-color: #3B82F6 !important;
}

.horario-tile.disabled {
    background-color: #4a1a1a !important;
    color: #ef4444 !important;
    border-color: #dc2626 !important;
}

.text-gray-600,
.text-gray-500,
.text-gray-700 {
    color: #d1d5db !important;
}

.text-gray-900 {
    color: #ffffff !important;
}

.bg-gray-50 {
    background-color: #2d2d2d !important;
}

.border-gray-200 {
    border-color: #404040 !important;
}

.border-gray-300 {
    border-color: #4b5563 !important;
}

.bg-gray-100 {
    background-color: #1f2937 !important;
}

.text-red-600 {
    color: #f87171 !important;
}

.text-red-700 {
    color: #f87171 !important;
}

.bg-red-100 {
    background-color: #7f1d1d !important;
    color: #f87171 !important;
}
</style>
@endif

@php $soloMode = \App\Models\Setting::get('site.solo_mode'); @endphp

    <form method="POST" action="{{ route('agendamentos.store-auto') }}" id="form-agendamento" class="grid lg:grid-cols-3 gap-6 items-start" novalidate>
        @csrf

        <!-- Campo Profissional (escondido se solo mode) -->
        @if($soloMode)
            <input type="hidden" name="profissional_id" value="{{ auth()->user()->profissional->id ?? 1 }}">
        @endif

        @if ($errors->any())
            <div class="lg:col-span-3 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
                <strong>Erros encontrados:</strong>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="lg:col-span-2 bg-brand-surface rounded-lg p-6 shadow-sm" style="border:1px solid var(--brand-border);">
            
            <!-- Etapa 1: Escolher Serviço -->
            <div id="etapa-servico" class="etapa">
                <h2 class="text-xl font-bold mb-4">1. Escolha o Serviço</h2>
                <input type="hidden" name="servico_id" id="servico_id_hidden" value="{{ old('servico_id') }}">
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4" id="servicos-container">
                    <!-- Serviço de Pacote em Destaque -->
                    @if($servicoPacote)
                        <label class="cursor-pointer servico-tile border-4 rounded-lg p-4 hover:border-opacity-60 transition-all duration-200 relative overflow-hidden" style="border-color: #10b981; background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);" data-id="{{ $servicoPacote->id }}" data-preco="0" data-is-pacote="true">
                            <input type="radio" name="servico_radio" value="{{ $servicoPacote->id }}" class="sr-only">
                            
                            <!-- Badge de Pacote -->
                            <div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                                SEU PACOTE
                            </div>
                            
                            <!-- Ícone de Pacote -->
                            <div class="servico-image mb-3 flex items-center justify-center h-32 bg-green-100 rounded-lg">
                                <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            
                            <h3 class="font-bold text-lg mb-2 text-green-800">{{ $servicoPacote->nome }}</h3>
                            <p class="text-gray-700 text-sm mb-3">{{ $servicoPacote->descricao }}</p>
                            
                            <!-- Info do Pacote -->
                            <div class="bg-white/70 rounded-lg p-3 mb-3">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-semibold text-green-700">Serviços restantes:</span>
                                    <span class="text-lg font-bold text-green-800">{{ $servicoPacote->pacote_info['restantes'] }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs text-gray-600">
                                    <span>{{ $servicoPacote->pacote_info['usados'] }} usados de {{ $servicoPacote->pacote_info['total'] }}</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Valor pago: R$ {{ number_format($servicoPacote->pacote_info['valor_pago'], 2, ',', '.') }}
                                </div>
                            </div>
                            
                            <p class="text-2xl font-bold text-green-600">GRÁTIS</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $servicoPacote->duracao_minutos }} min</p>
                        </label>
                    @endif
                    
                    @if($servicos->isEmpty() && !$servicoPacote)
                        <div class="col-span-full text-center p-8 bg-red-50 border border-red-200 rounded-lg">
                            <h3 class="text-lg font-semibold text-red-800 mb-2">Nenhum serviço disponível</h3>
                            <p class="text-red-600">Não há serviços cadastrados ou ativos no sistema.</p>
                            <p class="text-sm text-red-500 mt-2">Entre em contato com o administrador para cadastrar serviços.</p>
                        </div>
                    @else
                        @foreach($servicos as $servico)
                            <label class="cursor-pointer servico-tile border rounded-lg p-4 hover:border-opacity-60 transition-all duration-200 {{ $servicoPacote ? 'opacity-75' : '' }}" style="border-color: {{ \App\Models\Setting::get('brand.secondary', '#3b82f6') }}; background-color: {{ \App\Models\Setting::get('brand.surface', '#f8fafc') }};" data-id="{{ $servico->id }}" data-preco="{{ $servico->preco ?? 0 }}">
                                <input type="radio" name="servico_radio" value="{{ $servico->id }}" class="sr-only" {{ old('servico_id') == $servico->id ? 'checked' : '' }}>
                                
                                @if($servico->imagem_url)
                                    <div class="servico-image mb-3">
                                        <img src="{{ $servico->imagem_url }}" alt="{{ $servico->nome }}" class="w-full h-32 object-cover rounded-lg">
                                    </div>
                                @endif
                                
                                <h3 class="font-bold text-lg mb-2">{{ $servico->nome }}</h3>
                                <p class="text-gray-600 text-sm mb-2">{{ $servico->descricao }}</p>
                                <p class="text-2xl font-bold text-blue-600">R$ {{ number_format($servico->preco, 2, ',', '.') }}</p>
                                @if($servico->duracao_minutos)
                                    <p class="text-sm text-gray-500 mt-1">{{ $servico->duracao_minutos }} min</p>
                                @endif
                            </label>
                        @endforeach
                    @endif
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="button" id="btn-proximo-servico" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                        Próximo →
                    </button>
                </div>
            </div>
            
            <!-- Etapa 2: Escolher Profissional (só se não for solo mode) -->
            @if(!$soloMode)
            <div id="etapa-profissional" class="etapa hidden">
                <h2 class="text-xl font-bold mb-4">2. Escolha o Profissional</h2>
                <input type="hidden" name="profissional_id" id="profissional_id_hidden" value="{{ old('profissional_id') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4" id="profissionais-container">
                    @if($profissionais->isEmpty())
                        <div class="col-span-full text-center p-8 bg-red-50 border border-red-200 rounded-lg">
                            <h3 class="text-lg font-semibold text-red-800 mb-2">Nenhum profissional disponível</h3>
                            <p class="text-red-600">Não há profissionais cadastrados ou ativos no sistema.</p>
                            <p class="text-sm text-red-500 mt-2">Entre em contato com o administrador para cadastrar profissionais.</p>
                        </div>
                    @else
                        @foreach($profissionais as $profissional)
                            <label class="cursor-pointer profissional-tile border-2 border-gray-200 rounded-lg p-4 hover:border-blue-400 transition-all duration-200" data-id="{{ $profissional->id }}">
                                <input type="radio" name="profissional_radio" value="{{ $profissional->id }}" class="sr-only" {{ old('profissional_id') == $profissional->id ? 'checked' : '' }}>
                                
                                @if($profissional->avatar_url)
                                    <div class="profissional-image mb-3">
                                        <img src="{{ $profissional->avatar_url }}" alt="{{ $profissional->nome }}" class="w-20 h-20 rounded-full object-cover mx-auto">
                                    </div>
                                @else
                                    <div class="w-20 h-20 rounded-full bg-gray-300 flex items-center justify-center mx-auto mb-3">
                                        <span class="text-2xl text-gray-600">{{ substr($profissional->nome, 0, 1) }}</span>
                                    </div>
                                @endif
                                
                                <h3 class="font-semibold text-lg text-center">{{ $profissional->nome }}</h3>
                            </label>
                        @endforeach
                    @endif
                </div>
                
                <div class="flex justify-between mt-6">
                    <button type="button" id="btn-voltar-profissional" class="px-6 py-3 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors">
                        ← Voltar
                    </button>
                    <button type="button" id="btn-proximo-profissional" disabled 
                            class="px-6 py-2 rounded-lg font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                            style="background: var(--brand-secondary); color: var(--brand-on-secondary);">
                        Próximo →
                    </button>
                </div>
            </div>
            @endif
            
            <!-- Etapa 3: Escolher Data -->
            <div id="etapa-data" class="etapa hidden">
                <h2 class="text-xl font-bold mb-4">@if(!$soloMode) 3 @else 2 @endif. Escolha a Data</h2>
                <input type="hidden" name="data" id="data" value="{{ old('data') }}">
                <div id="datas-container" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-7 gap-3">
                    <!-- Datas serão inseridas via JavaScript -->
                </div>
                
                <div class="mt-6 flex justify-between">
                    <button type="button" id="btn-voltar-data" class="px-6 py-3 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors">
                        ← Voltar
                    </button>
                    <button type="button" id="btn-proximo-data" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                        Próximo →
                    </button>
                </div>
            </div>

            <!-- Etapa 3: Escolher Horário -->
            <div id="etapa-horario" class="etapa hidden">
                <h2 class="text-xl font-bold mb-4">@if(!$soloMode) 3 @else 2 @endif. Escolha o Horário</h2>
                <input type="hidden" name="hora" id="hora" value="{{ old('hora') }}">
                <div id="horarios-container" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                    <!-- Horários serão inseridos via JavaScript -->
                </div>
                
                <div id="mensagem-horario" class="mt-4 p-3 rounded-lg hidden"></div>
                
                <div class="mt-6 flex justify-between">
                    <button type="button" id="btn-voltar-horario" class="px-6 py-3 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors">
                        ← Voltar
                    </button>
                    <button type="button" id="btn-proximo-horario" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                        Próximo →
                    </button>
                </div>
            </div>

            <!-- Etapa 4: Confirmar -->
            <div id="etapa-confirmar" class="etapa hidden">
                <h2 class="text-xl font-bold mb-4">@if(!$soloMode) 4 @else 3 @endif. Confirmar Agendamento</h2>
                
                <!-- Campo Cliente Avulso (apenas para usuários não logados) -->
                @if(!auth()->check())
                    <div class="mb-6">
                        <label for="cliente_avulso" class="block text-sm font-medium text-gray-700 mb-2">Seu Nome *</label>
                        <input type="text" 
                               id="cliente_avulso" 
                               name="cliente_avulso" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                               placeholder="Digite seu nome completo"
                               value="{{ old('cliente_avulso') }}"
                               required>
                        @error('cliente_avulso')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <!-- Campo oculto para usuários logados -->
                    <input type="hidden" name="cliente_logado" value="true">
                @endif
                
                <div class="bg-gray-50 p-6 rounded-lg">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="font-semibold">Serviço:</span>
                            <span id="confirm-servico">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold">Data:</span>
                            <span id="confirm-data">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold">Horário:</span>
                            <span id="confirm-horario">-</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold text-blue-600">
                            <span>Total:</span>
                            <span id="confirm-preco">-</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-between">
                    <button type="button" id="btn-voltar-confirmar" class="px-6 py-3 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors">
                        ← Voltar
                    </button>
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors">
                        Confirmar Agendamento
                    </button>
                </div>
            </div>
        </div>

        <!-- Resumo -->
        <div class="lg:col-span-1">
            <div class="bg-brand-surface rounded-lg p-6 shadow-sm sticky top-6" style="border:1px solid var(--brand-border);">
                <h3 class="text-lg font-bold mb-4">Resumo do Agendamento</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-600">Serviço:</span>
                        <p class="font-semibold" id="resumo-servico">Não selecionado</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Data:</span>
                        <p class="font-semibold" id="resumo-data">Não selecionada</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Horário:</span>
                        <p class="font-semibold" id="resumo-horario">Não selecionado</p>
                    </div>
                    <div class="border-t pt-3">
                        <span class="text-gray-600">Total:</span>
                        <p class="text-2xl font-bold text-blue-600" id="resumo-preco">R$ 0,00</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</div>
</div>

<style>
.etapa {
    transition: all 0.3s ease;
}

.etapa.hidden {
    display: none;
}

.servico-tile {
    transition: all 0.2s ease;
    cursor: pointer;
    border-width: 1px !important;
}

.servico-tile:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-width: 1.5px !important;
}

.servico-tile input:checked + div {
    border-color: {{ \App\Models\Setting::get('brand.secondary', '#3b82f6') }} !important;
    background-color: {{ \App\Models\Setting::get('brand.surface', '#f8fafc') }} !important;
    border-width: 2px !important;
}

.servico-tile input:checked {
    border-color: {{ \App\Models\Setting::get('brand.secondary', '#3b82f6') }} !important;
    border-width: 2px !important;
}

.data-tile {
    transition: all 0.2s ease;
    cursor: pointer;
}

.data-tile:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.data-tile.selected {
    background-color: #3B82F6;
    color: white;
    border-color: #3B82F6;
}

.horario-tile {
    transition: all 0.2s ease;
    cursor: pointer;
}

.horario-tile:hover:not(.disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.horario-tile.selected {
    background-color: #3B82F6;
    color: white;
    border-color: #3B82F6;
}

.horario-tile.disabled {
    background-color: #FEE2E2;
    color: #DC2626;
    border-color: #FCA5A5;
    cursor: not-allowed;
    opacity: 0.6;
}

.mensagem-sucesso {
    background-color: #D1FAE5;
    color: #065F46;
    border: 1px solid #6EE7B7;
}

.mensagem-erro {
    background-color: #FEE2E2;
    color: #991B1B;
    border: 1px solid #FCA5A5;
}

.mensagem-sugestao {
    background-color: var(--brand-primary);
    color: var(--brand-on-primary);
    border: 2px solid var(--brand-secondary);
    box-shadow: 0 4px 6px -1px rgba(0 0 0 / 0.1);
}

@media (prefers-color-scheme: dark) {
    .mensagem-sugestao {
        background-color: var(--brand-primary);
        color: var(--brand-on-primary);
        border: 2px solid var(--brand-secondary);
        box-shadow: 0 4px 6px -1px rgba(0 0 0 / 0.3);
    }
}

.horario-tile.selected {
    background-color: #3B82F6 !important;
    color: white !important;
    border-color: #3B82F6 !important;
}

.horario-tile.indisponivel-selecionado {
    background-color: #EF4444 !important;
    color: white !important;
    border-color: #EF4444 !important;
    transform: scale(1.05) !important;
    box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3) !important;
}

.horario-tile.sugerido {
    background-color: #10B981 !important;
    color: white !important;
    border-color: #10B981 !important;
    transform: scale(1.05) !important;
    box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3) !important;
    animation: pulse 1s infinite !important;
}

@keyframes pulse {
    0% {
        transform: scale(1.05);
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
    }
    50% {
        transform: scale(1.1);
        box-shadow: 0 6px 8px rgba(16, 185, 129, 0.5);
    }
    100% {
        transform: scale(1.05);
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
    }
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in-delay {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out;
}

.animate-fade-in-delay {
    animation: fade-in-delay 0.8s ease-out 0.2s both;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Página carregada - iniciando sistema...');
    console.log('Solo mode:', {{ $soloMode ? 'true' : 'false' }});
    
    // Variáveis globais
    let profissionalSelecionado = null;
    let servicoSelecionado = null;
    let dataSelecionada = null;
    let horarioSelecionado = null;
    let etapaAtual = 1;
    
    // Elementos do DOM
    const profissionaisContainer = document.getElementById('profissionais-container');
    const servicosContainer = document.getElementById('servicos-container');
    const datasContainer = document.getElementById('datas-container');
    const horariosContainer = document.getElementById('horarios-container');
    const mensagemHorario = document.getElementById('mensagem-horario');
    
    console.log('Elementos encontrados:');
    console.log('profissionaisContainer:', profissionaisContainer);
    console.log('servicosContainer:', servicosContainer);
    console.log('datasContainer:', datasContainer);
    console.log('horariosContainer:', horariosContainer);
    
    // Botões
    const btnProximoProfissional = document.getElementById('btn-proximo-profissional');
    const btnVoltarProfissional = document.getElementById('btn-voltar-profissional');
    const btnProximoServico = document.getElementById('btn-proximo-servico');
    const btnVoltarData = document.getElementById('btn-voltar-data');
    const btnProximoData = document.getElementById('btn-proximo-data');
    const btnVoltarHorario = document.getElementById('btn-voltar-horario');
    const btnProximoHorario = document.getElementById('btn-proximo-horario');
    const btnVoltarConfirmar = document.getElementById('btn-voltar-confirmar');
    
    // Resumo
    const resumoServico = document.getElementById('resumo-servico');
    const resumoData = document.getElementById('resumo-data');
    const resumoHorario = document.getElementById('resumo-horario');
    const resumoPreco = document.getElementById('resumo-preco');
    
    // Confirmar
    const confirmServico = document.getElementById('confirm-servico');
    const confirmData = document.getElementById('confirm-data');
    const confirmHorario = document.getElementById('confirm-horario');
    const confirmPreco = document.getElementById('confirm-preco');

    // Etapas
    const soloMode = {{ $soloMode ? 'true' : 'false' }};
    const etapas = soloMode ? {
        1: document.getElementById('etapa-servico'),
        2: document.getElementById('etapa-data'),
        3: document.getElementById('etapa-horario'),
        4: document.getElementById('etapa-confirmar')
    } : {
        1: document.getElementById('etapa-servico'),
        2: document.getElementById('etapa-profissional'),
        3: document.getElementById('etapa-data'),
        4: document.getElementById('etapa-horario'),
        5: document.getElementById('etapa-confirmar')
    };

    // Função para mostrar/ocultar etapas
    function mostrarEtapa(numero) {
        Object.keys(etapas).forEach(key => {
            if (key == numero) {
                etapas[key].classList.remove('hidden');
            } else {
                etapas[key].classList.add('hidden');
            }
        });
        etapaAtual = numero;
    }

    // Função para atualizar resumo
    function atualizarResumo() {
        console.log('Atualizando resumo:', { servicoSelecionado, dataSelecionada, horarioSelecionado });
        
        if (servicoSelecionado) {
            resumoServico.textContent = servicoSelecionado.nome;
            confirmServico.textContent = servicoSelecionado.nome;
            
            if (servicoSelecionado.isPacote) {
                resumoPreco.textContent = 'GRÁTIS (Pacote)';
                confirmPreco.textContent = 'GRÁTIS (Pacote)';
                confirmPreco.className = 'text-xl font-bold text-green-600';
            } else {
                resumoPreco.textContent = `R$ ${servicoSelecionado.preco.toFixed(2).replace('.', ',')}`;
                confirmPreco.textContent = `R$ ${servicoSelecionado.preco.toFixed(2).replace('.', ',')}`;
                confirmPreco.className = 'text-xl font-bold text-blue-600';
            }
        }
        
        if (dataSelecionada) {
            resumoData.textContent = formatarData(dataSelecionada);
            confirmData.textContent = formatarData(dataSelecionada);
        }
        
        if (horarioSelecionado) {
            resumoHorario.textContent = horarioSelecionado;
            confirmHorario.textContent = horarioSelecionado;
        }
    }

    // Função para formatar data
    function formatarData(dataString) {
        const data = new Date(dataString);
        const dias = ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sáb'];
        const meses = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
        
        return `${dias[data.getDay()]}, ${data.getDate()} de ${meses[data.getMonth()]} de ${data.getFullYear()}`;
    }

    // Gerar datas
    function gerarDatas() {
        console.log('Gerando datas...');
        datasContainer.innerHTML = '';
        const hoje = new Date();
        const datas = [];
        
        for (let i = 0; i < 14; i++) {
            const data = new Date(hoje);
            data.setDate(hoje.getDate() + i);
            
            // Pular domingos
            if (data.getDay() === 0) continue;
            
            const dataString = data.toISOString().split('T')[0];
            datas.push(dataString);
            
            const tile = document.createElement('div');
            tile.className = 'data-tile border-2 border-gray-200 rounded-lg p-3 text-center cursor-pointer';
            tile.innerHTML = `
                <div class="font-bold">${['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sáb'][data.getDay()]}</div>
                <div class="text-2xl">${data.getDate()}</div>
                <div class="text-sm">${['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'][data.getMonth()]}</div>
            `;
            tile.dataset.data = dataString;
            
            tile.addEventListener('click', function() {
                selecionarData(dataString);
            });
            
            datasContainer.appendChild(tile);
        }
        
        return datas;
    }

    // Selecionar data
    function selecionarData(dataString) {
        console.log('Data selecionada:', dataString);
        dataSelecionada = dataString;
        
        // ATUALIZAR O CAMPO HIDDEN
        document.getElementById('data').value = dataString;
        console.log('Campo data atualizado para:', document.getElementById('data').value);
        
        // Atualizar visual
        document.querySelectorAll('.data-tile').forEach(tile => {
            tile.classList.remove('selected');
        });
        event.target.closest('.data-tile').classList.add('selected');
        
        // Atualizar resumo
        atualizarResumo();
        
        // Gerar horários
        gerarHorarios(dataString);
        
        // Habilitar botão próximo
        btnProximoData.disabled = false;
        
        // AVANÇAR AUTOMATICAMENTE
        setTimeout(() => {
            console.log('Avançando automaticamente para horários...');
            mostrarEtapa(3);
        }, 300);
    }

    // Gerar horários
    async function gerarHorarios(dataString) {
        if (!servicoSelecionado) {
            console.log('Serviço não selecionado, não pode gerar horários');
            return;
        }
        
        console.log('Gerando horários para:', dataString);
        horariosContainer.innerHTML = '<p class="col-span-full text-center">Carregando horários...</p>';
        
        try {
            const response = await fetch('/api/horarios-disponiveis-dia', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    profissional_id: 1, // Ajustar para pegar profissional correto
                    servico_id: servicoSelecionado.id,
                    data: dataString
                })
            });
            
            const data = await response.json();
            const horarios = data.timeslots || [];
            
            console.log('Horários recebidos:', horarios);
            horariosContainer.innerHTML = '';
            
            horarios.forEach(horario => {
                const tile = document.createElement('div');
                tile.className = `horario-tile border-2 rounded-lg p-3 text-center cursor-pointer ${
                    horario.disponivel ? 'border-gray-200 hover:border-blue-400' : 'border-gray-200 hover:border-orange-400'
                }`;
                tile.textContent = horario.hora;
                tile.dataset.horario = horario.hora;
                tile.dataset.disponivel = horario.disponivel;
                
                // Debug: mostrar disponibilidade
                console.log(`Horário ${horario.hora}: disponível = ${horario.disponivel}`);
                
                // Permitir clique em QUALQUER horário (disponível ou não)
                tile.addEventListener('click', function() {
                    console.log(`Clicou em ${horario.hora} - disponível: ${horario.disponivel}`);
                    
                    // Se não estiver disponível, mostrar sugestões
                    if (!horario.disponivel) {
                        mostrarSugestoesHorario(horario.hora);
                    } else {
                        selecionarHorario(horario.hora);
                    }
                });
                
                horariosContainer.appendChild(tile);
            });
            
        } catch (error) {
            console.error('Erro ao carregar horários:', error);
            horariosContainer.innerHTML = '<p class="col-span-full text-center text-red-600">Erro ao carregar horários</p>';
        }
    }

    // Mostrar sugestões de horários disponíveis
    function mostrarSugestoesHorario(horarioSelecionado) {
        console.log(`mostrarSugestoesHorario chamado para: ${horarioSelecionado}`);
        
        const todosHorarios = Array.from(document.querySelectorAll('.horario-tile'));
        const indexSelecionado = todosHorarios.findIndex(tile => tile.dataset.horario === horarioSelecionado);
        
        console.log(`Índice selecionado: ${indexSelecionado}`);
        console.log(`Total de horários: ${todosHorarios.length}`);
        
        // Limpar estados anteriores
        todosHorarios.forEach(tile => {
            tile.classList.remove('indisponivel-selecionado', 'sugerido', 'bg-green-100', 'border-green-500');
            // Restaurar texto original se tiver sido alterado
            if (tile.dataset.textoOriginal) {
                tile.textContent = tile.dataset.textoOriginal;
                delete tile.dataset.textoOriginal;
            }
        });
        
        // Destacar horário selecionado como indisponível (vermelho)
        const tileSelecionado = todosHorarios[indexSelecionado];
        if (tileSelecionado) {
            tileSelecionado.classList.add('indisponivel-selecionado');
            // Salvar o texto original
            const textoOriginal = tileSelecionado.textContent;
            tileSelecionado.dataset.textoOriginal = textoOriginal;
            // Mudar para "INDISPONÍVEL"
            tileSelecionado.textContent = 'INDISPONÍVEL';
            console.log(`Destacando horário indisponível: ${tileSelecionado.dataset.horario}`);
            console.log(`Classes do tile: ${tileSelecionado.className}`);
            console.log(`Estilo computado:`, window.getComputedStyle(tileSelecionado));
        }
        
        // Buscar próximo disponível
        let proximoDisponivel = null;
        let proximoTile = null;
        for (let i = indexSelecionado + 1; i < todosHorarios.length; i++) {
            console.log(`Verificando horário ${i}: ${todosHorarios[i].dataset.horario} - disponível: ${todosHorarios[i].dataset.disponivel}`);
            if (todosHorarios[i].dataset.disponivel === 'true') {
                proximoDisponivel = todosHorarios[i].dataset.horario;
                proximoTile = todosHorarios[i];
                console.log(`Próximo disponível encontrado: ${proximoDisponivel}`);
                break;
            }
        }
        
        // Buscar anterior disponível
        let anteriorDisponivel = null;
        let anteriorTile = null;
        for (let i = indexSelecionado - 1; i >= 0; i--) {
            console.log(`Verificando horário ${i}: ${todosHorarios[i].dataset.horario} - disponível: ${todosHorarios[i].dataset.disponivel}`);
            if (todosHorarios[i].dataset.disponivel === 'true') {
                anteriorDisponivel = todosHorarios[i].dataset.horario;
                anteriorTile = todosHorarios[i];
                console.log(`Anterior disponível encontrado: ${anteriorDisponivel}`);
                break;
            }
        }
        
        // Montar mensagem de sugestão
        let mensagem = `⚠️ Horário ${horarioSelecionado} não disponível.\n`;
        if (proximoDisponivel) {
            mensagem += `✅ Próximo disponível: ${proximoDisponivel}\n`;
        }
        if (anteriorDisponivel) {
            mensagem += `✅ Anterior disponível: ${anteriorDisponivel}`;
        }
        
        console.log(`Mensagem de sugestão: ${mensagem}`);
        
        // Mostrar mensagem com estilo melhorado
        mensagemHorario.innerHTML = mensagem.replace(/\n/g, '<br>');
        mensagemHorario.classList.remove('hidden');
        mensagemHorario.className = 'mt-4 p-4 rounded-lg mensagem-sugestao font-semibold';
        
        // Destacar horários sugeridos (verde)
        if (proximoTile) {
            proximoTile.classList.add('sugerido');
            console.log(`Destacando horário sugerido: ${proximoTile.dataset.horario}`);
            console.log(`Classes do tile sugerido: ${proximoTile.className}`);
            console.log(`Estilo computado do sugerido:`, window.getComputedStyle(proximoTile));
        }
        if (anteriorTile) {
            anteriorTile.classList.add('sugerido');
            console.log(`Destacando horário sugerido: ${anteriorTile.dataset.horario}`);
            console.log(`Classes do tile sugerido: ${anteriorTile.className}`);
            console.log(`Estilo computado do sugerido:`, window.getComputedStyle(anteriorTile));
        }
        
        // Auto-selecionar o próximo disponível após 2 segundos
        if (proximoDisponivel) {
            console.log(`Auto-selecionando ${proximoDisponivel} em 2 segundos`);
            setTimeout(() => {
                selecionarHorario(proximoDisponivel);
                mensagemHorario.classList.add('hidden');
            }, 2000);
        }
    }

    // Selecionar horário
    function selecionarHorario(horario) {
        console.log('Horário selecionado:', horario);
        horarioSelecionado = horario;
        
        // ATUALIZAR O CAMPO HIDDEN
        document.getElementById('hora').value = horario;
        console.log('Campo hora atualizado para:', document.getElementById('hora').value);
        
        // Atualizar visual - limpar todos os estados
        document.querySelectorAll('.horario-tile').forEach(tile => {
            tile.classList.remove('selected', 'indisponivel-selecionado', 'sugerido', 'bg-green-100', 'border-green-500');
            // Restaurar texto original se tiver sido alterado
            if (tile.dataset.textoOriginal) {
                tile.textContent = tile.dataset.textoOriginal;
                delete tile.dataset.textoOriginal;
            }
            if (tile.dataset.horario === horario) {
                tile.classList.add('selected');
            }
        });
        
        // Limpar mensagem de sugestões
        mensagemHorario.classList.add('hidden');
        
        // Atualizar resumo
        atualizarResumo();
        
        // Habilitar botão próximo
        btnProximoHorario.disabled = false;
        
        // AVANÇAR AUTOMATICAMENTE
        setTimeout(() => {
            console.log('Avançando automaticamente para confirmação...');
            mostrarEtapa(4);
        }, 300);
        
        // Limpar mensagem
        mensagemHorario.classList.add('hidden');
    }

    // Event listeners para profissionais (só se não for solo mode)
    if (!soloMode && profissionaisContainer) {
        profissionaisContainer.addEventListener('click', function(e) {
            console.log('Clique no container de profissionais');
            const tile = e.target.closest('.profissional-tile');
            if (!tile) {
                console.log('Tile não encontrado');
                return;
            }
            
            const radio = tile.querySelector('input[type="radio"]');
            if (!radio) {
                console.log('Radio não encontrado');
                return;
            }
            
            console.log('Profissional clicado:', radio.value);
            
            // Limpar seleção anterior
            document.querySelectorAll('.profissional-tile').forEach(t => {
                t.classList.remove('border-blue-500', 'bg-blue-50');
                t.classList.add('border-gray-200');
            });
            
            // Selecionar profissional
            radio.checked = true;
            tile.classList.remove('border-gray-200');
            tile.classList.add('border-blue-500', 'bg-blue-50');
            
            // ATUALIZAR O CAMPO HIDDEN
            document.getElementById('profissional_id_hidden').value = radio.value;
            console.log('Campo profissional_id_hidden atualizado para:', document.getElementById('profissional_id_hidden').value);
            
            profissionalSelecionado = {
                id: parseInt(radio.value),
                nome: tile.querySelector('h3').textContent
            };
            
            console.log('Profissional selecionado:', profissionalSelecionado);
            
            // Habilitar botão próximo
            btnProximoProfissional.disabled = false;
            
            // AVANÇAR AUTOMATICAMENTE
            setTimeout(() => {
                console.log('Avançando automaticamente para próxima etapa...');
                mostrarEtapa(3); // Vai para etapa de data
                gerarDatas();
            }, 300);
        });
    }

    // Event listeners para serviços
    servicosContainer.addEventListener('click', function(e) {
        console.log('Clique no container de serviços');
        const tile = e.target.closest('.servico-tile');
        if (!tile) {
            console.log('Tile não encontrado');
            return;
        }
        
        const radio = tile.querySelector('input[type="radio"]');
        if (!radio) {
            console.log('Radio não encontrado');
            return;
        }
        
        console.log('Serviço clicado:', radio.value);
        
        // Limpar seleção anterior
        document.querySelectorAll('.servico-tile').forEach(t => {
            t.style.borderWidth = '1px';
            t.style.borderColor = '{{ \App\Models\Setting::get('brand.secondary', '#3b82f6') }}';
            t.style.backgroundColor = '{{ \App\Models\Setting::get('brand.surface', '#f8fafc') }}';
        });
        
        // Selecionar serviço
        radio.checked = true;
        tile.style.borderWidth = '2px';
        tile.style.borderColor = '{{ \App\Models\Setting::get('brand.secondary', '#3b82f6') }}';
        tile.style.backgroundColor = '{{ \App\Models\Setting::get('brand.surface', '#f8fafc') }}';
        
        // ATUALIZAR O CAMPO HIDDEN
        document.getElementById('servico_id_hidden').value = radio.value;
        console.log('Campo servico_id_hidden atualizado para:', document.getElementById('servico_id_hidden').value);
        
        // Verificar se é serviço de pacote
        const isPacote = tile.dataset.isPacote === 'true';
        
        servicoSelecionado = {
            id: radio.value,
            nome: tile.querySelector('h3').textContent,
            preco: parseFloat(tile.dataset.preco),
            isPacote: isPacote
        };
        
        console.log('Serviço selecionado:', servicoSelecionado);
        
        // Atualizar resumo
        atualizarResumo();
        
        // Habilitar botão próximo
        btnProximoServico.disabled = false;
        
        // AVANÇAR AUTOMATICAMENTE
        setTimeout(() => {
            console.log('Avançando automaticamente para próxima etapa...');
            const proximaEtapa = soloMode ? 2 : 2; // Se não for solo mode, vai para profissional (etapa 2)
            mostrarEtapa(proximaEtapa);
            if (soloMode) gerarDatas(); // Só gera datas se for solo mode
        }, 300);
    });

    // Event listeners para botões
    if (btnProximoProfissional) {
        btnProximoProfissional.addEventListener('click', function() {
            console.log('Próximo da etapa profissional');
            mostrarEtapa(3); // Vai para data
            gerarDatas();
        });
    }
    
    if (btnVoltarProfissional) {
        btnVoltarProfissional.addEventListener('click', function() {
            console.log('Voltar para etapa serviço');
            mostrarEtapa(1);
        });
    }
    
    btnProximoServico.addEventListener('click', function() {
        console.log('Próximo da etapa serviço');
        const proximaEtapa = soloMode ? 2 : 2; // Profissional ou data
        mostrarEtapa(proximaEtapa);
        if (soloMode) gerarDatas();
    });

    btnVoltarData.addEventListener('click', function() {
        console.log('Voltar para etapa anterior');
        const etapaAnterior = soloMode ? 1 : 2; // Serviço ou profissional
        mostrarEtapa(etapaAnterior);
    });

    btnProximoData.addEventListener('click', function() {
        console.log('Próximo da etapa data');
        const proximaEtapa = soloMode ? 3 : 4;
        mostrarEtapa(proximaEtapa);
    });

    btnVoltarHorario.addEventListener('click', function() {
        console.log('Voltar para etapa data');
        mostrarEtapa(soloMode ? 2 : 3);
    });

    btnProximoHorario.addEventListener('click', function() {
        console.log('Próximo da etapa horário');
        const proximaEtapa = soloMode ? 4 : 5;
        mostrarEtapa(proximaEtapa);
    });

    btnVoltarConfirmar.addEventListener('click', function() {
        console.log('Voltar para etapa horário');
        mostrarEtapa(soloMode ? 3 : 4);
    });

    // Verificar campos antes do submit
    document.getElementById('form-agendamento').addEventListener('submit', function(e) {
        console.log('=== VERIFICAÇÃO FINAL ===');
        console.log('Profissional ID:', document.getElementById('profissional_id_hidden')?.value || 'Solo mode');
        console.log('Serviço ID:', document.getElementById('servico_id_hidden').value);
        console.log('Data:', document.getElementById('data').value);
        console.log('Hora:', document.getElementById('hora').value);
        console.log('Cliente Avulso:', document.getElementById('cliente_avulso').value);
        
        // Se algum campo obrigatório estiver vazio, impedir o envio
        if (!document.getElementById('servico_id_hidden').value || 
            !document.getElementById('data').value || 
            !document.getElementById('hora').value ||
            !document.getElementById('cliente_avulso').value ||
            (!soloMode && !document.getElementById('profissional_id_hidden').value)) {
            
            console.error('Campos obrigatórios não preenchidos!');
            alert('Por favor, preencha todos os campos obrigatórios.');
            e.preventDefault();
            return false;
        }
        
        console.log('Todos os campos preenchidos. Enviando formulário...');
    });

    // Inicialização
    console.log('Inicializando sistema...');
    mostrarEtapa(1); // Sempre começa com serviços (etapa 1)
});
</script>
@endsection
