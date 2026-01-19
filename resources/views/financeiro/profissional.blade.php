@extends('layouts.app')

@section('title', 'Meus Ganhos')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-4 sm:mb-6 px-4 sm:px-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-vm-navy-800">💰 Meus Ganhos</h1>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Acompanhe seus ganhos em tempo real</p>
    </div>

    <!-- Cards de Ganhos por Período (compactos) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6 px-2 sm:px-0">
        <div class="flex items-center gap-3 p-3 rounded-lg shadow-sm" style="background: rgba(34,197,94,0.06); border-left: 4px solid var(--brand-success);">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(34,197,94,0.15);">
                <span class="text-sm">📅</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-gray-600">Hoje</div>
                <div class="mt-1 text-sm font-bold text-gray-900">R$ {{ number_format($ganhoDia, 2, ',', '.') }}</div>
                @if($preConcluidoDia > 0)
                    <div class="mt-1 text-xs text-gray-500">+ R$ {{ number_format($preConcluidoDia, 2, ',', '.') }} aguardando</div>
                @endif
                <div class="mt-1 text-xs text-gray-500">{{ $atendimentosDia }} atendimentos</div>
            </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-lg shadow-sm" style="background: rgba(250,130,49,0.04); border-left: 4px solid var(--brand-warning);">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(250,130,49,0.12);">
                <span class="text-sm">📊</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-gray-600">Esta Semana</div>
                <div class="mt-1 text-sm font-bold text-gray-900">R$ {{ number_format($ganhoSemana, 2, ',', '.') }}</div>
                @if($preConcluidoSemana > 0)
                    <div class="mt-1 text-xs text-gray-500">+ R$ {{ number_format($preConcluidoSemana, 2, ',', '.') }} aguardando</div>
                @endif
                <div class="mt-1 text-xs text-gray-500">{{ $atendimentosSemana }} atendimentos</div>
            </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-lg shadow-sm" style="background: rgba(59,130,246,0.04); border-left: 4px solid var(--brand-primary);">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(59,130,246,0.12);">
                <span class="text-sm">📈</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-gray-600">Este Mês</div>
                <div class="mt-1 text-sm font-bold text-gray-900">R$ {{ number_format($ganhoMes, 2, ',', '.') }}</div>
                @if($preConcluidoMes > 0)
                    <div class="mt-1 text-xs text-gray-500">+ R$ {{ number_format($preConcluidoMes, 2, ',', '.') }} aguardando</div>
                @endif
                <div class="mt-1 text-xs text-gray-500">{{ $atendimentosMes }} atendimentos</div>
            </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-lg shadow-sm" style="background: rgba(139,92,246,0.04); border-left: 4px solid var(--brand-accent);">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(139,92,246,0.12);">
                <span class="text-sm">🎯</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-gray-600">Este Ano</div>
                <div class="mt-1 text-sm font-bold text-gray-900">R$ {{ number_format($ganhoAno, 2, ',', '.') }}</div>
                @if($preConcluidoAno > 0)
                    <div class="mt-1 text-xs text-gray-500">+ R$ {{ number_format($preConcluidoAno, 2, ',', '.') }} aguardando</div>
                @endif
                <div class="mt-1 text-xs text-gray-500">{{ $atendimentosAno }} atendimentos</div>
            </div>
        </div>
    </div>

    <!-- Evolução Mensal -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 mx-4 sm:mx-0">
        <h3 class="text-lg sm:text-xl font-bold mb-6 text-vm-navy-800">📈 Evolução dos Últimos 12 Meses</h3>
        
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
                                        <div class="h-full rounded-full transition-all duration-500" style="width: {{ $percentual }}%; background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary));"></div>
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

    <!-- Legenda -->
    <div class="bg-vm-navy-50 rounded-xl p-4 sm:p-6 mx-4 sm:mx-0">
        <h4 class="font-semibold text-vm-navy-800 mb-3">ℹ️ Informações Importantes</h4>
        <div class="space-y-2 text-sm text-gray-700">
            <p>✓ <strong class="text-green-600">Valores Confirmados:</strong> Já foram aprovados pela proprietária</p>
            <p>⏳ <strong class="text-orange-600">Valores Aguardando:</strong> Seus atendimentos finalizados, aguardando confirmação da proprietária</p>
            <p>💡 <strong>Dica:</strong> Os valores "aguardando" geralmente são confirmados em até 24h</p>
        </div>
    </div>
</div>
@endsection

