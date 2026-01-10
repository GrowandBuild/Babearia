@extends('layouts.cliente')

@section('title', 'Agendar Atendimento')

@section('content')
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-vm-gold to-vm-gold-600 shadow-lg mb-6 rounded-lg">
        <div class="p-6">
            <h1 class="text-3xl font-bold text-white drop-shadow-lg" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);">Agendar Atendimento</h1>
            <p class="text-white font-semibold mt-2" style="text-shadow: 0 1px 2px rgba(0,0,0,0.3);">Escolha o profissional, serviço e horário desejado</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            <strong>Erros encontrados:</strong>
            <ul class="list-disc list-inside mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('agendamentos.store-auto') }}" id="form-agendamento" class="bg-white rounded-lg shadow-xl border-2 border-gray-200 p-6">
        @csrf

        <div class="space-y-6">
            <!-- Profissional -->
            <div>
                <label class="block text-base font-bold text-gray-900 mb-3">Escolha o Profissional *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="profissionais-container">
                    @foreach($profissionais as $prof)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="profissional_id" value="{{ $prof->id }}" 
                                   {{ old('profissional_id') == $prof->id ? 'checked' : '' }}
                                   class="peer sr-only" required>
                            <div class="flex flex-col items-center gap-3 p-4 border-2 rounded-lg peer-checked:shadow-lg hover:shadow-md transition-all" 
                                 style="border-color: #9CA3AF; background-color: #FFFFFF;">
                                <x-avatar 
                                    :src="$prof->avatar_url" 
                                    :name="$prof->nome" 
                                    size="lg" />
                                <div class="text-center">
                                    <div class="font-bold text-gray-900 text-lg">{{ $prof->nome }}</div>
                                    <div class="text-sm text-gray-700 font-medium">{{ $prof->percentual_comissao }}% comissão</div>
                                </div>
                                <div class="peer-checked:block hidden">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" style="color: #D4AF37;">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('profissional_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Serviço -->
            <div>
                <label class="block text-base font-bold text-gray-900 mb-2">Escolha o Serviço *</label>
                <select name="servico_id" id="servico_id" required 
                        class="block w-full rounded-lg border-2 border-gray-400 bg-white shadow-sm focus:border-vm-gold focus:ring-vm-gold text-base py-3 px-4 font-medium text-gray-900">
                    <option value="" class="text-gray-500">Selecione um serviço...</option>
                    @foreach($servicos as $servico)
                        <option value="{{ $servico->id }}" 
                                data-duracao="{{ $servico->duracao_minutos ?? 30 }}"
                                data-preco="{{ $servico->preco }}"
                                {{ old('servico_id') == $servico->id ? 'selected' : '' }}>
                            {{ $servico->nome }} - R$ {{ number_format($servico->preco, 2, ',', '.') }}
                            @if($servico->duracao_minutos) ({{ $servico->duracao_minutos }} min) @endif
                        </option>
                    @endforeach
                </select>
                @error('servico_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Data e Hora -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-base font-bold text-gray-900 mb-2">Data *</label>
                    <input type="date" name="data" id="data" value="{{ old('data', now()->format('Y-m-d')) }}" required
                           min="{{ now()->format('Y-m-d') }}"
                           class="block w-full rounded-lg border-2 border-gray-400 bg-white shadow-sm focus:border-vm-gold focus:ring-vm-gold text-base py-3 px-4 font-medium text-gray-900">
                    @error('data')
                        <p class="text-red-600 text-sm font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-base font-bold text-gray-900 mb-2">Horário Desejado *</label>
                    <input type="time" name="hora" id="hora" value="{{ old('hora') }}" required
                           step="1800"
                           class="block w-full rounded-lg border-2 border-gray-400 bg-white shadow-sm focus:border-vm-gold focus:ring-vm-gold text-base py-3 px-4 font-medium text-gray-900">
                    @error('hora')
                        <p class="text-red-600 text-sm font-semibold mt-1">{{ $message }}</p>
                    @enderror
                    
                    <!-- Sugestões de horários -->
                    <div id="sugestoes-container" class="mt-3 hidden bg-orange-50 border-2 border-orange-300 rounded-lg p-3">
                        <p class="text-sm font-bold text-gray-900 mb-2">Horário não disponível. Sugestões:</p>
                        <div id="sugestoes-buttons" class="flex flex-wrap gap-2"></div>
                    </div>
                    
                    <!-- Status do horário -->
                    <div id="status-horario" class="mt-2 text-sm font-bold"></div>
                </div>
            </div>

            <!-- Nome do Cliente -->
            <div>
                <label class="block text-base font-bold text-gray-900 mb-2">Seu Nome *</label>
                <input type="text" name="cliente_avulso" value="{{ old('cliente_avulso', auth()->user()->name ?? '') }}" required
                       class="block w-full rounded-lg border-2 border-gray-400 bg-white shadow-sm focus:border-vm-gold focus:ring-vm-gold text-base py-3 px-4 font-medium text-gray-900">
                @error('cliente_avulso')
                    <p class="text-red-600 text-sm font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Observações -->
            <div>
                <label class="block text-base font-bold text-gray-900 mb-2">Observações (opcional)</label>
                <textarea name="observacoes" rows="3"
                          class="block w-full rounded-lg border-2 border-gray-400 bg-white shadow-sm focus:border-vm-gold focus:ring-vm-gold text-base py-3 px-4 font-medium text-gray-900">{{ old('observacoes') }}</textarea>
                @error('observacoes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botões -->
            <div class="flex gap-3 pt-4">
                <button type="submit" id="btn-submit" 
                        class="flex-1 px-6 py-3 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition-all"
                        style="background: linear-gradient(to right, #D4AF37, #B89627);"
                        onmouseover="this.style.background='linear-gradient(to right, #B89627, #8B711D)'"
                        onmouseout="this.style.background='linear-gradient(to right, #D4AF37, #B89627)'">
                    Confirmar Agendamento
                </button>
                <a href="{{ route('agendamentos.agenda') }}" 
                   class="px-6 py-3 bg-gray-400 text-white font-bold rounded-lg hover:bg-gray-500 shadow-md transition-all">
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const profissionalSelect = document.querySelectorAll('input[name="profissional_id"]');
    const servicoSelect = document.getElementById('servico_id');
    const dataInput = document.getElementById('data');
    const horaInput = document.getElementById('hora');
    const sugestoesContainer = document.getElementById('sugestoes-container');
    const sugestoesButtons = document.getElementById('sugestoes-buttons');
    const statusHorario = document.getElementById('status-horario');
    const form = document.getElementById('form-agendamento');
    let verificando = false;

    function verificarHorario() {
        const profissionalId = document.querySelector('input[name="profissional_id"]:checked')?.value;
        const servicoId = servicoSelect.value;
        const data = dataInput.value;
        const hora = horaInput.value;

        if (!profissionalId || !servicoId || !data || !hora) {
            statusHorario.innerHTML = '';
            sugestoesContainer.classList.add('hidden');
            return;
        }

        if (verificando) return;
        verificando = true;
        statusHorario.innerHTML = '<span class="text-blue-700 font-bold">⏳ Verificando disponibilidade...</span>';

        fetch('{{ route("api.horarios-disponiveis") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                profissional_id: profissionalId,
                servico_id: servicoId,
                data: data,
                hora_desejada: hora
            })
        })
        .then(response => response.json())
        .then(data => {
            verificando = false;
            if (data.disponivel) {
                statusHorario.innerHTML = '<span class="text-green-700 font-bold text-lg">✓ Horário disponível!</span>';
                sugestoesContainer.classList.add('hidden');
            } else {
                statusHorario.innerHTML = '<span class="text-red-700 font-bold text-lg">✗ Horário não disponível</span>';
                
                if (data.sugestoes && data.sugestoes.length > 0) {
                    sugestoesButtons.innerHTML = '';
                    data.sugestoes.forEach(sugestao => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'px-4 py-2 text-white font-bold rounded-lg shadow-md transition-all';
                        btn.style.backgroundColor = '#D4AF37';
                        btn.onmouseover = function() { this.style.backgroundColor = '#B89627'; };
                        btn.onmouseout = function() { this.style.backgroundColor = '#D4AF37'; };
                        btn.textContent = sugestao.hora + (sugestao.tipo === 'proximo' ? ' (Próximo)' : ' (Anterior)');
                        btn.onclick = function() {
                            horaInput.value = sugestao.hora;
                            verificarHorario();
                        };
                        sugestoesButtons.appendChild(btn);
                    });
                    sugestoesContainer.classList.remove('hidden');
                } else {
                    sugestoesContainer.classList.add('hidden');
                }
            }
        })
        .catch(error => {
            verificando = false;
            console.error('Erro:', error);
            statusHorario.innerHTML = '<span class="text-red-700 font-bold">Erro ao verificar disponibilidade</span>';
        });
    }

    // Adicionar listeners
    profissionalSelect.forEach(radio => {
        radio.addEventListener('change', verificarHorario);
    });
    servicoSelect.addEventListener('change', verificarHorario);
    dataInput.addEventListener('change', verificarHorario);
    horaInput.addEventListener('change', verificarHorario);
    horaInput.addEventListener('input', function() {
        // Verificar após 1 segundo de inatividade
        clearTimeout(window.verificarTimeout);
        window.verificarTimeout = setTimeout(verificarHorario, 1000);
    });
});
</script>

<style>
    /* Estilos para cards de profissionais */
    input[name="profissional_id"]:checked + div {
        border-color: #D4AF37 !important;
        background-color: #FBF8EC !important;
    }
    
    input[name="profissional_id"]:checked + div svg {
        display: block !important;
        color: #D4AF37;
    }
    
    /* Melhorar contraste dos inputs */
    input[type="date"],
    input[type="time"],
    input[type="text"],
    select,
    textarea {
        background-color: #FFFFFF !important;
        border-color: #9CA3AF !important;
        color: #111827 !important;
    }
    
    input[type="date"]:focus,
    input[type="time"]:focus,
    input[type="text"]:focus,
    select:focus,
    textarea:focus {
        border-color: #D4AF37 !important;
        outline: none;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1) !important;
    }
    
    /* Cards de profissionais com hover */
    label:hover > div {
        border-color: #D4AF37 !important;
    }
</style>
@endsection

