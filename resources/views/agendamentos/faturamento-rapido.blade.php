@extends('layouts.app')

@section('title', 'Faturamento Rápido')

@section('content')
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Faturamento Rápido</h3>
        
        <form id="formFaturamento">
            <input type="hidden" id="agendamento_id" name="agendamento_id" value="{{ $agendamento->id }}">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cliente</label>
                <div class="text-gray-600 font-medium">{{ $agendamento->getNomeClienteAttribute() }}</div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Serviço</label>
                <div class="text-gray-600 text-sm">{{ $agendamento->servicos->first()->nome ?? 'Serviço' }} - R$ {{ number_format($agendamento->servicos->first()->preco ?? 0, 2, ',', '.') }}</div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Forma de Pagamento</label>
                <select id="forma_pagamento" name="forma_pagamento" required class="w-full border rounded-md px-3 py-2">
                    <option value="">Selecione...</option>
                    <option value="pix">PIX</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="debito">Débito</option>
                    <option value="credito">Crédito</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Valor (R$)</label>
                <input type="number" id="valor" name="valor" step="0.01" required 
                       class="w-full border rounded-md px-3 py-2" 
                       value="{{ number_format($agendamento->servicos->first()->preco ?? 0, 2, '.', '') }}">
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                    Confirmar Pagamento
                </button>
                <button type="button" onclick="fecharModal()" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function fecharModal() {
    window.location.href = '/agenda';
}

document.getElementById('formFaturamento').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    fetch('/agendamentos/' + data.agendamento_id + '/finalizar-pagamento', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Pagamento registrado com sucesso!');
            window.location.href = '/agenda';
        } else {
            alert('Erro: ' + (result.error || 'Ocorreu um erro'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Ocorreu um erro ao processar o pagamento');
    });
});
</script>
@endsection
