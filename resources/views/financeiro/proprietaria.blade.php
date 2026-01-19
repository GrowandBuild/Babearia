@extends('layouts.app')

@section('title', 'Relatório Financeiro')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-4 sm:mb-6 px-4 sm:px-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-vm-navy-800">💰 Relatório Financeiro</h1>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Visão completa do desempenho financeiro da esmalteria</p>
    </div>

    <!-- Cards de Faturamento por Período (compactos) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6 px-2 sm:px-0">
        <div class="flex items-center gap-3 p-3 rounded-lg shadow-sm" style="background: rgba(34,197,94,0.06); border-left: 4px solid var(--brand-success);">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(34,197,94,0.15);">
                <span class="text-sm">📅</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-gray-600">Hoje</div>
                <div class="mt-1 text-sm font-bold text-gray-900">R$ {{ number_format($totalDia, 2, ',', '.') }}</div>
                @if($pendentesDia > 0)
                    <div class="mt-1">
                        <span class="inline-block text-xs font-semibold text-white bg-orange-500 rounded-full px-2">{{ $pendentesDia }} pend.</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-lg shadow-sm" style="background: rgba(250,130,49,0.04); border-left: 4px solid var(--brand-warning);">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(250,130,49,0.12);">
                <span class="text-sm">📊</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-gray-600">Esta Semana</div>
                <div class="mt-1 text-sm font-bold text-gray-900">R$ {{ number_format($totalSemana, 2, ',', '.') }}</div>
                @if($pendentesSemana > 0)
                    <div class="mt-1">
                        <span class="inline-block text-xs font-semibold text-white bg-orange-500 rounded-full px-2">{{ $pendentesSemana }} pend.</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-lg shadow-sm" style="background: rgba(59,130,246,0.04); border-left: 4px solid var(--brand-primary);">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(59,130,246,0.12);">
                <span class="text-sm">📈</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-gray-600">Este Mês</div>
                <div class="mt-1 text-sm font-bold text-gray-900">R$ {{ number_format($totalMes, 2, ',', '.') }}</div>
                @if($pendentesMes > 0)
                    <div class="mt-1">
                        <span class="inline-block text-xs font-semibold text-white bg-orange-500 rounded-full px-2">{{ $pendentesMes }} pend.</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-lg shadow-sm" style="background: rgba(139,92,246,0.04); border-left: 4px solid var(--brand-accent);">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(139,92,246,0.12);">
                <span class="text-sm">🎯</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-gray-600">Este Ano ({{ now()->year }})</div>
                <div class="mt-1 text-sm font-bold text-gray-900">R$ {{ number_format($totalAno, 2, ',', '.') }}</div>
                @if($pendentesAno > 0)
                    <div class="mt-1">
                        <span class="inline-block text-xs font-semibold text-white bg-orange-500 rounded-full px-2">{{ $pendentesAno }} pend.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Evolução Mensal -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 mx-4 sm:mx-0">
        <h3 class="text-lg sm:text-xl font-bold mb-6 text-vm-navy-800">📈 Evolução Mensal da Empresa</h3>
        
        <div class="space-y-3">
            @foreach($evolucaoMensal as $mes)
                <div class="flex items-center gap-3">
                    <div class="w-20 sm:w-24 text-xs sm:text-sm font-medium text-gray-600">{{ $mes['mes'] }}</div>
                    <div class="flex-1">
                        <div class="bg-gray-200 rounded-full h-8 relative overflow-hidden">
                            @php
                                $maxValor = collect($evolucaoMensal)->max('total');
                                $percentual = $maxValor > 0 ? ($mes['total'] / $maxValor) * 100 : 0;
                            @endphp
                            <div class="h-full rounded-full transition-all duration-500" 
                                 style="width: {{ $percentual }}%; background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary));"></div>
                            <div class="absolute inset-0 flex items-center px-3">
                                <span class="text-xs sm:text-sm font-bold {{ $mes['total'] > 0 ? 'text-white' : 'text-gray-600' }}">
                                    R$ {{ number_format($mes['total'], 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 px-4 sm:px-0">
        <a href="{{ route('agendamentos.agenda') }}" class="rounded-lg p-4 flex items-center gap-3 bg-brand-primary text-brand-on-primary hover:shadow-md transition-all">
            <div>
                <h3 class="text-lg font-bold">Ver Agenda</h3>
                <p class="text-sm opacity-90 mt-1">Acompanhar atendimentos</p>
            </div>
            <svg class="w-10 h-10 opacity-90 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </a>

        <a href="{{ route('dashboard') }}" class="rounded-lg p-4 flex items-center gap-3 bg-brand-secondary text-brand-on-secondary hover:shadow-md transition-all">
            <div>
                <h3 class="text-lg font-bold">Dashboard Completo</h3>
                <p class="text-sm opacity-90 mt-1">Visão geral e ranking</p>
            </div>
            <svg class="w-10 h-10 opacity-90 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </a>
    </div>
</div>
@endsection

