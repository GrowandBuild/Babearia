@extends('layouts.app')

@section('title', 'Dashboard - Cliente')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Bem-vindo, {{ $user->name }}!</h1>
                <p class="text-gray-600">Aqui você pode acompanhar seus agendamentos e histórico.</p>
            </div>

            <!-- Cards Resumo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-blue-100 text-sm">Total Gasto</p>
                            <p class="text-2xl font-bold">R$ {{ number_format($totalGasto, 2, ',', '.') }}</p>
                        </div>
                        <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-green-100 text-sm">Próximos Agendamentos</p>
                            <p class="text-2xl font-bold">{{ $proximosAgendamentos->count() }}</p>
                        </div>
                        <svg class="w-8 h-8 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-purple-100 text-sm">Total de Agendamentos</p>
                            <p class="text-2xl font-bold">{{ $agendamentos->count() }}</p>
                        </div>
                        <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Próximos Agendamentos -->
            @if($proximosAgendamentos->count() > 0)
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Seus Próximos Agendamentos</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($proximosAgendamentos as $agendamento)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center mb-2">
                                    @if($agendamento->servico)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                            {{ $agendamento->servico->nome }}
                                        </span>
                                    @endif
                                    @if($agendamento->profissional)
                                        <span class="ml-2 text-sm text-gray-600">
                                            com {{ $agendamento->profissional->nome }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-900">
                                    <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($agendamento->data_hora)->format('d/m/Y') }}</p>
                                    <p><strong>Hora:</strong> {{ \Carbon\Carbon::parse($agendamento->data_hora)->format('H:i') }}</p>
                                </div>
                                <div class="mt-2">
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">
                                        {{ ucfirst($agendamento->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Histórico de Agendamentos -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Histórico de Agendamentos</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serviço</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profissional</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($agendamentos as $agendamento)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ \Carbon\Carbon::parse($agendamento->data_hora)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $agendamento->servico->nome ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $agendamento->profissional->nome ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @foreach($agendamento->pagamentos as $pagamento)
                                            R$ {{ number_format($pagamento->valor, 2, ',', '.') }}
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($agendamento->status == 'concluido') 
                                                bg-green-100 text-green-800
                                            @elseif($agendamento->status == 'cancelado')
                                                bg-red-100 text-red-800
                                            @elseif($agendamento->status == 'confirmado')
                                                bg-blue-100 text-blue-800
                                            @else
                                                bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ ucfirst($agendamento->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Nenhum agendamento encontrado no período
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
