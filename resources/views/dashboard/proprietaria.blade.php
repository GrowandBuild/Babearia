@extends('layouts.app')

@section('title', 'Dashboard Financeiro')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <!-- Filtro de Período -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4 sm:mb-6 mx-4 sm:mx-0">
        <div class="p-4 sm:p-6">
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                <label for="data_inicio" class="sr-only">Data Início</label>
                <div class="flex items-center gap-2">
                    <input id="data_inicio" type="date" name="data_inicio" value="{{ $dataInicio }}"
                           class="px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-1 focus:ring-offset-0 focus:ring-offset-white focus:ring-var sm:text-sm"
                           style="width:150px">
                    <input id="data_fim" type="date" name="data_fim" value="{{ $dataFim }}"
                           class="px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-1 focus:ring-offset-0 focus:ring-offset-white focus:ring-var sm:text-sm"
                           style="width:150px">
                </div>

                <button type="submit" class="ml-2 inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-semibold shadow-sm" style="background: var(--brand-btn-primary-bg); color: var(--brand-btn-primary-text);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path></svg>
                    Filtrar
                </button>

                <a href="{{ route('dashboard') }}" class="ml-1 text-xs text-gray-500 hover:underline">Limpar</a>
            </form>
        </div>
    </div>

    <!-- Cards Resumo -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4 px-2 sm:px-0">
        {{-- Compact financial cards: smaller, denser, modern --}}
        <div class="flex items-center gap-3 p-3 rounded-lg shadow-sm" style="background: rgba(34,197,94,0.06); border-left: 4px solid var(--brand-success);">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(34,197,94,0.15);">
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M3 3h14v4H3zM3 9h10v8H3z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-gray-600">Confirmado</div>
                <div class="mt-1 text-sm font-bold text-gray-900">R$ {{ number_format($totalEmpresa, 2, ',', '.') }}</div>
            </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-lg shadow-sm" style="background: rgba(250,130,49,0.04); border-left: 4px solid var(--brand-warning);">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(250,130,49,0.12);">
                <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-gray-600">Aguardando</div>
                <div class="mt-1 text-sm font-bold text-gray-900">R$ {{ number_format($totalPreConcluido, 2, ',', '.') }} @if($agendamentosPendentes > 0)<span class="ml-2 inline-block text-xs font-semibold text-white bg-orange-500 rounded-full px-2">{{ $agendamentosPendentes }}</span>@endif</div>
            </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-lg shadow-sm" style="background: rgba(59,130,246,0.04); border-left: 4px solid var(--brand-primary);">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(59,130,246,0.12);">
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3h12v4H4zM4 9h8v8H4z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-gray-600">Profissionais</div>
                <div class="mt-1 text-sm font-bold text-gray-900">R$ {{ number_format($profissionais->sum('total'), 2, ',', '.') }}</div>
            </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-lg shadow-sm" style="background: rgba(139,92,246,0.04); border-left: 4px solid var(--brand-accent);">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(139,92,246,0.12);">
                <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4h14v3H3zM3 9h14v7H3z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-gray-600">Faturamento</div>
                <div class="mt-1 text-sm font-bold text-gray-900">R$ {{ number_format($totalGeral, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Desempenho por Profissional -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Desempenho por Profissional</h3>
                <div class="space-y-3">
                    @forelse($profissionais as $prof)
                        <div class="border-l-4 border-indigo-500 pl-4 py-2">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center gap-3">
                                    <x-avatar 
                                        :src="$prof['avatar_url']" 
                                        :name="$prof['nome']" 
                                        size="sm" />
                                    <div>
                                        <div class="font-semibold text-gray-800">{{ $prof['nome'] }}</div>
                                        <div class="text-xs text-gray-500">Comissão: {{ $prof['percentual'] }}%</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-gray-800">R$ {{ number_format($prof['total'], 2, ',', '.') }}</div>
                                    <div class="text-xs text-gray-500">
                                        Comissão: R$ {{ number_format($prof['total_comissao'], 2, ',', '.') }}<br>
                                        Gorjetas: R$ {{ number_format($prof['total_gorjetas'], 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Nenhum atendimento no período</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Ranking de Clientes -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Top 10 Clientes (Lucro Gerado)</h3>
                <div class="space-y-2">
                    @forelse($rankingClientes as $index => $cliente)
                        <div class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded">
                            <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-yellow-400 to-orange-500 text-white rounded-full flex items-center justify-center font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-800">{{ $cliente['nome'] }}</div>
                                <div class="text-xs text-gray-500">{{ $cliente['total_atendimentos'] }} atendimentos</div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold text-green-600">R$ {{ number_format($cliente['lucro_gerado'], 2, ',', '.') }}</div>
                                <div class="text-xs text-gray-500">Total: R$ {{ number_format($cliente['total_gasto'], 2, ',', '.') }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Nenhum cliente cadastrado ainda</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Agendamentos de Hoje -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-4 sm:px-0">
        <div class="p-3 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold mb-4">Agendamentos de Hoje</h3>
            <div class="space-y-3">
                @forelse($agendamentosHoje as $agendamento)
                    <div class="rounded-lg p-3 sm:p-4 
                        @if($agendamento->status == 'concluido') 
                            bg-gradient-to-r from-green-500 to-green-600 text-white
                        @elseif($agendamento->status == 'pre_concluido') 
                            bg-gradient-to-r from-orange-400 to-orange-500 text-white
                        @elseif($agendamento->status == 'agendado') 
                            bg-gradient-to-r from-blue-500 to-blue-600 text-white
                        @else 
                            bg-gradient-to-r from-gray-400 to-gray-500 text-white
                        @endif
                        shadow-md hover:shadow-lg transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="text-xl sm:text-2xl font-bold">
                                    {{ $agendamento->data_hora->format('H:i') }}
                                </div>
                                
                                <!-- Avatar do Profissional -->
                                <div class="flex-shrink-0">
                                    @if($agendamento->profissional && $agendamento->profissional->user && $agendamento->profissional->user->avatar)
                                        <img src="{{ asset('storage/' . $agendamento->profissional->user->avatar) }}" 
                                             alt="{{ $agendamento->profissional->nome }}" 
                                             class="w-10 h-10 rounded-full object-cover ring-2 ring-white shadow-lg">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center ring-2 ring-white shadow-lg">
                                            <span class="text-white font-bold text-sm">{{ strtoupper(substr($agendamento->profissional->nome, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div>
                                    <div class="font-semibold text-sm sm:text-base">{{ $agendamento->nome_cliente }}</div>
                                    @if($agendamento->servico)
                                        <div class="text-xs sm:text-sm opacity-90">{{ $agendamento->servico->nome }}</div>
                                    @else
                                        <div class="text-xs sm:text-sm opacity-70">Serviço não especificado</div>
                                    @endif
                                    <div class="text-xs opacity-80">{{ $agendamento->profissional->nome }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 text-xs rounded-full font-bold bg-white/20 backdrop-blur-sm border border-white/30">
                                    @if($agendamento->status == 'concluido') ✓ Confirmado
                                    @elseif($agendamento->status == 'pre_concluido') ⏳ Pré-Concluído
                                    @elseif($agendamento->status == 'agendado') ⏰ Agendado
                                    @else ✕ Cancelado
                                    @endif
                                </span>
                                @if($agendamento->status == 'pre_concluido')
                                    <a href="{{ route('agendamentos.agenda') }}" 
                                       class="px-2 sm:px-3 py-1 bg-white text-green-700 text-xs font-semibold rounded hover:bg-green-50 whitespace-nowrap shadow-md">
                                        Ver Detalhes
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        Nenhum agendamento para hoje
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

