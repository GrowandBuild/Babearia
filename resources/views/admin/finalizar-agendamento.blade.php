@extends('layouts.app')

@section('title', 'Finalizar Atendimento')

@section('content')
<div class="container mx-auto p-4 max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Finalizar Atendimento</h1>

        @if($agendamento)
            <form method="POST" action="{{ route('admin.agenda.finalizar') }}">
                @csrf
                
                <input type="hidden" name="agendamento_id" value="{{ $agendamento->id }}">
                
                <!-- Informações do Cliente -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-semibold mb-3">Informações do Cliente</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="font-medium">Cliente:</span>
                            <span class="ml-2">{{ $agendamento->getNomeClienteAttribute() }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Data/Hora:</span>
                            <span class="ml-2">{{ $agendamento->data_hora->format('d/m/Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Profissional:</span>
                            <span class="ml-2">{{ $agendamento->profissional->getNomeAttribute() }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Status:</span>
                            <span class="ml-2 px-2 py-1 rounded text-xs font-medium
                                @if($agendamento->status == 'concluido') bg-green-100 text-green-800
                                @elseif($agendamento->status == 'cancelado') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($agendamento->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Serviços Realizados -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Serviços Realizados</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        @if($agendamento->servicos->count() > 0)
                            @foreach($agendamento->servicos as $servico)
                                <div class="flex justify-between items-center py-2 border-b last:border-b-0">
                                    <div>
                                        <span class="font-medium">{{ $servico->nome }}</span>
                                        @if($servico->duracao_minutos)
                                            <span class="text-gray-500 text-sm ml-2">({{ $servico->duracao_minutos }} min)</span>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="font-semibold">R$ {{ number_format($servico->preco_cobrado ?? $servico->preco, 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500">Nenhum serviço registrado</p>
                        @endif
                    </div>
                </div>

                <!-- Serviços Adicionais -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Serviços Adicionais</h3>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach(\App\Models\Servico::where('ativo', true)->get() as $servico)
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="servicos_adicionais[]" value="{{ $servico->id }}" 
                                       class="mr-3 h-4 w-4 text-blue-600">
                                <div class="flex-1">
                                    <div class="font-medium">{{ $servico->nome }}</div>
                                    <div class="text-sm text-gray-500">{{ $servico->duracao_minutos ?? 30 }} min</div>
                                </div>
                                <div class="font-semibold text-green-600">
                                    R$ {{ number_format($servico->preco, 2, ',', '.') }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Desconto e Pagamento -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Desconto (R$)</label>
                        <input type="number" name="desconto" min="0" step="0.01" 
                               class="w-full border rounded-md px-3 py-2" placeholder="0,00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Forma de Pagamento *</label>
                        <select name="forma_pagamento" required class="w-full border rounded-md px-3 py-2">
                            <option value="">Selecione...</option>
                            <option value="pix">PIX</option>
                            <option value="dinheiro">Dinheiro</option>
                            <option value="debito">Cartão de Débito</option>
                            <option value="credito">Cartão de Crédito</option>
                        </select>
                    </div>
                </div>

                <!-- Observações -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Observações da Finalização</label>
                    <textarea name="observacoes_finalizacao" rows="3" 
                              class="w-full border rounded-md px-3 py-2" 
                              placeholder="Observações sobre o atendimento..."></textarea>
                </div>

                <!-- Resumo do Valor -->
                <div class="border-t pt-4 mb-6">
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center text-lg">
                            <span class="font-semibold">Total a Pagar:</span>
                            <span id="valor-final" class="text-2xl font-bold text-green-600">R$ 0,00</span>
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 font-semibold">
                        Finalizar Atendimento
                    </button>
                    <a href="{{ route('admin.agenda') }}" class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-md hover:bg-gray-400 font-semibold text-center">
                        Voltar para Agenda
                    </a>
                </div>
            </form>
        @else
            <div class="bg-red-50 border border-red-200 p-4 rounded-lg">
                <h3 class="text-red-800 font-semibold">Agendamento não encontrado</h3>
                <p class="text-red-600 mt-2">O agendamento solicitado não existe ou foi removido.</p>
                <a href="{{ route('admin.agenda') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800">
                    Voltar para Agenda
                </a>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcular total dinamicamente
    function calcularTotal() {
        let total = 0;
        
        // Serviços adicionais
        document.querySelectorAll('input[name="servicos_adicionais[]"]:checked').forEach(checkbox => {
            const label = checkbox.closest('label');
            const precoText = label.querySelector('.text-green-600').textContent;
            const preco = parseFloat(precoText.replace('R$ ', '').replace('.', '').replace(',', '.'));
            total += preco;
        });
        
        // Desconto
        const desconto = parseFloat(document.querySelector('input[name="desconto"]').value.replace(',', '.')) || 0;
        
        const valorFinal = Math.max(0, total - desconto);
        document.getElementById('valor-final').textContent = `R$ ${valorFinal.toFixed(2).replace('.', ',')}`;
    }
    
    // Event listeners
    document.querySelectorAll('input[name="servicos_adicionais[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', calcularTotal);
    });
    
    document.querySelector('input[name="desconto"]').addEventListener('input', calcularTotal);
    
    // Calcular inicial
    calcularTotal();
});
</script>
@endsection
