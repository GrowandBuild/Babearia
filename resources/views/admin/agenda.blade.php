@extends('layouts.app')

@section('title', 'Agenda - Admin')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Agenda (Admin)</h1>

    <!-- Modal de Finalização de Atendimento -->
    <div id="modalFinalizar" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">Finalizar Atendimento</h3>
            
            <form id="formFinalizar">
                <input type="hidden" id="agendamento_id" name="agendamento_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cliente</label>
                    <div id="cliente_info" class="text-gray-600 font-medium"></div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Serviços Realizados</label>
                    <div id="servicos_info" class="text-gray-600 text-sm"></div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Serviços Adicionais</label>
                    <select id="servicos_adicionais" name="servicos_adicionais[]" multiple class="w-full border rounded-md px-3 py-2">
                        @foreach(\App\Models\Servico::where('ativo', true)->get() as $servico)
                            <option value="{{ $servico->id }}">{{ $servico->nome }} - R$ {{ number_format($servico->preco, 2, ',', '.') }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Segure Ctrl para selecionar múltiplos</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Desconto (R$)</label>
                    <input type="number" id="desconto" name="desconto" min="0" step="0.01" class="w-full border rounded-md px-3 py-2" placeholder="0,00">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Forma de Pagamento</label>
                    <select id="forma_pagamento" name="forma_pagamento" required class="w-full border rounded-md px-3 py-2">
                        <option value="">Selecione...</option>
                        <option value="pix">PIX</option>
                        <option value="dinheiro">Dinheiro</option>
                        <option value="debito">Cartão de Débito</option>
                        <option value="credito">Cartão de Crédito</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                    <textarea id="observacoes_finalizacao" name="observacoes_finalizacao" rows="3" class="w-full border rounded-md px-3 py-2" placeholder="Observações sobre o atendimento..."></textarea>
                </div>

                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-4">
                        <span class="font-semibold">Total:</span>
                        <span id="valor_final" class="text-xl font-bold text-green-600">R$ 0,00</span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        Finalizar Atendimento
                    </button>
                    <button type="button" onclick="fecharModal()" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- FullCalendar CSS/JS (v5 global build via CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/resource-timegrid.min.js"></script>

    <div id="calendar"></div>
</div>

<style>
    /* Ajustes visuais semelhantes ao mock */
    #calendar { max-width: 1200px; margin: 0 auto; }
    .fc-resource { display:flex; align-items:center; gap:8px }
    .fc-resource img { width:28px; height:28px; border-radius:50%; object-fit:cover }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: [ 'resourceTimeGrid' ],
        initialView: 'resourceTimeGridDay',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'resourceTimeGridDay,resourceTimeGridWeek'
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        resources: function(fetchInfo, successCallback, failureCallback) {
            fetch('/admin/agenda/events?resources=1&start=' + fetchInfo.startStr + '&end=' + fetchInfo.endStr)
                .then(r => r.json())
                .then(data => successCallback(data.resources))
                .catch(err => failureCallback(err));
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch('/admin/agenda/events?start=' + fetchInfo.startStr + '&end=' + fetchInfo.endStr)
                .then(r => r.json())
                .then(data => successCallback(data.events))
                .catch(err => failureCallback(err));
        },
        resourceLabelContent: function(arg) {
            var html = '';
            if (arg.resource.extendedProps && arg.resource.extendedProps.avatar) {
                html += '<img src="' + arg.resource.extendedProps.avatar + '"/> ';
            }
            html += '<strong>' + arg.resource.title + '</strong>';
            return { html: html };
        },
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        height: 'auto',
        nowIndicator: true,
        selectable: false,
        editable: false,
        eventDidMount: function(info) {
            // adicionar tooltip simples
            info.el.title = info.event.title + ' - ' + (info.event.extendedProps.status || '');
            
            // Debug para verificar o status
            console.log('Agendamento ID:', info.event.id, 'Status:', info.event.extendedProps.status);
            console.log('Elemento do evento:', info.el);
            
            // Adicionar botão de finalização para TODOS os eventos (temporariamente para teste)
            const finalizarBtn = document.createElement('button');
            finalizarBtn.innerHTML = 'Finalizar';
            finalizarBtn.style.cssText = `
                margin-left: 8px;
                padding: 2px 6px;
                background: #10b981;
                color: white;
                border: none;
                border-radius: 4px;
                font-size: 11px;
                cursor: pointer;
                font-weight: bold;
            `;
            finalizarBtn.onclick = function(e) {
                e.stopPropagation();
                console.log('Botão clicado para agendamento:', info.event.id);
                abrirModalFinalizar(info.event.id);
            };
            
            // Adicionar o botão no final do elemento do evento
            info.el.appendChild(finalizarBtn);
            
            console.log('Botão adicionado com sucesso!');
        }
    });

    calendar.render();
});

// Funções para o modal de finalização
function abrirModalFinalizar(eventId) {
    // Buscar dados do agendamento
    fetch(`/admin/agenda/events`)
        .then(r => r.json())
        .then(data => {
            const event = data.events.find(e => e.id === eventId);
            if (event) {
                document.getElementById('agendamento_id').value = eventId;
                document.getElementById('cliente_info').textContent = event.title.split(' - ')[0];
                
                // Mostrar serviços
                const servicos = event.extendedProps.servicos || [];
                document.getElementById('servicos_info').innerHTML = servicos.length > 0 
                    ? servicos.map(s => `${s.nome} - R$ ${s.preco}`).join('<br>') 
                    : 'Nenhum serviço registrado';
                
                // Resetar campos
                document.getElementById('servicos_adicionais').selectedIndex = 0;
                document.getElementById('desconto').value = '';
                document.getElementById('forma_pagamento').selectedIndex = 0;
                document.getElementById('observacoes_finalizacao').value = '';
                
                calcularTotal();
                
                // Mostrar modal
                document.getElementById('modalFinalizar').classList.remove('hidden');
                document.getElementById('modalFinalizar').classList.add('flex');
            }
        });
}

function fecharModal() {
    document.getElementById('modalFinalizar').classList.add('hidden');
    document.getElementById('modalFinalizar').classList.remove('flex');
}

function calcularTotal() {
    const desconto = parseFloat(document.getElementById('desconto').value) || 0;
    const servicosAdicionais = Array.from(document.getElementById('servicos_adicionais').selectedOptions);
    
    let valorExtras = 0;
    servicosAdicionais.forEach(option => {
        const preco = parseFloat(option.text.split(' - R$ ')[1]) || 0;
        valorExtras += preco;
    });
    
    const valorFinal = Math.max(0, valorExtras - desconto);
    document.getElementById('valor_final').textContent = `R$ ${valorFinal.toFixed(2).replace('.', ',')}`;
}

// Event listeners
document.getElementById('servicos_adicionais').addEventListener('change', calcularTotal);
document.getElementById('desconto').addEventListener('input', calcularTotal);

// Submit do formulário
document.getElementById('formFinalizar').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    // Converter arrays para JSON
    if (data.servicos_adicionais) {
        data.servicos_adicionais = Array.isArray(data.servicos_adicionais) ? data.servicos_adicionais : [data.servicos_adicionais];
    }
    
    fetch('/admin/agenda/finalizar-atendimento', {
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
            alert('Atendimento finalizado com sucesso! Valor: R$ ' + result.valor_final.toFixed(2).replace('.', ','));
            fecharModal();
            // Recarregar o calendário para atualizar o status
            calendar.refetchEvents();
        } else {
            alert('Erro: ' + (result.error || 'Ocorreu um erro ao finalizar o atendimento'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Ocorreu um erro ao finalizar o atendimento');
    });
});
</script>
@endpush

@endsection
