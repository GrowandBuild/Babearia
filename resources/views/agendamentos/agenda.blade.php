@extends('layouts.app')

@section('title', 'Agenda')

@section('content')
<div class="max-w-full mx-auto sm:px-6 lg:px-8">
    <!-- Header com Navegação e Filtros -->
    <div class="shadow-lg mb-4 sm:mb-6 rounded-lg site-header" style="background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary)); color: var(--brand-header-text);">
        <div class="p-4 sm:p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Navegação de Data -->
                <div class="flex items-center gap-3 flex-wrap">
                          <a href="{{ route('agendamentos.agenda', ['data' => \Carbon\Carbon::parse($data)->subDay()->format('Y-m-d'), 'profissional_id' => $profissionalId]) }}" 
                              class="p-2 rounded-lg transition-colors shadow-sm" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.06); color: var(--brand-header-text);">
                        <svg class="w-5 h-5 text-vm-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <a href="{{ route('agendamentos.agenda', ['data' => today()->format('Y-m-d'), 'profissional_id' => $profissionalId]) }}" 
                       class="px-4 py-2 rounded-lg font-bold transition-colors shadow-sm" style="background: rgba(255,255,255,0.08); color: var(--brand-header-text); border: 1px solid rgba(255,255,255,0.06);">
                        Hoje
                    </a>
                          <a href="{{ route('agendamentos.agenda', ['data' => \Carbon\Carbon::parse($data)->addDay()->format('Y-m-d'), 'profissional_id' => $profissionalId]) }}" 
                              class="p-2 rounded-lg transition-colors shadow-sm" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.06); color: var(--brand-header-text);">
                        <svg class="w-5 h-5 text-vm-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <div class="px-4 py-2 rounded-lg" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.04);">
                           <input type="date" 
                               value="{{ $data }}" 
                               onchange="window.location.href='{{ route('agendamentos.agenda') }}?data=' + this.value + '&profissional_id={{ $profissionalId }}'"
                               class="border-0 focus:ring-0 font-bold text-sm" style="background: transparent; color: var(--brand-header-text);">
                    </div>
                    <div class="px-4 py-2 rounded-lg" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.04);">
                        <span class="font-bold text-lg" style="color: var(--brand-header-text);">
                            {{ \Carbon\Carbon::parse($data)->locale('pt_BR')->translatedFormat('d M Y - l') }}
                        </span>
                    </div>
                </div>

                <!-- Filtros e Ações -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    @can('isProprietaria')
                    <form method="GET" action="{{ route('agendamentos.agenda') }}" class="flex gap-2">
                        <input type="hidden" name="data" value="{{ $data }}">
                        <select name="profissional_id" 
                                onchange="this.form.submit()"
                                class="rounded-lg border-2 border-white/50 bg-white text-gray-900 font-semibold shadow-md px-3 py-2 focus:ring-2 focus:ring-white focus:border-white">
                            <option value="">Todos os Profissionais</option>
                            @foreach($profissionais as $prof)
                                <option value="{{ $prof->id }}" {{ $profissionalId == $prof->id ? 'selected' : '' }}>
                                    {{ $prof->nome }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    @endcan
                    
                    <a href="{{ route('agendamentos.create') }}" 
                       class="px-5 py-2 rounded-lg font-bold flex items-center gap-2 transition-all" style="background: var(--brand-secondary); color: var(--brand-on-secondary); box-shadow: 0 6px 18px rgba(0,0,0,0.12);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agendar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade de Agenda -->
    <div class="bg-brand-surface rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr style="background: var(--brand-surface); border-bottom: 1px solid var(--brand-border);">
                        <th class="sticky left-0 z-20 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider border-r" style="min-width:80px; border-right:1px solid var(--brand-border); color: var(--text-light);">
                            Horário
                        </th>
                        @php
                            $solo = \App\Models\Setting::get('site.solo_mode');
                            if ($solo) {
                                // quando em modo 'trabalho sozinho', exibir apenas o primeiro profissional (ou o vinculado)
                                $profissionaisParaExibir = $profissionais->slice(0,1);
                            } else {
                                $profissionaisParaExibir = $profissionalId 
                                    ? $profissionais->where('id', $profissionalId) 
                                    : $profissionais;
                            }
                        @endphp
                        @foreach($profissionaisParaExibir as $prof)
                            <th class="px-4 py-3 text-center border-r min-w-[200px]" style="border-right:1px solid var(--brand-border); color: var(--text-light);">
                                <div class="flex flex-col items-center gap-2">
                                    <x-avatar 
                                        :src="$prof->avatar_url" 
                                        :name="$prof->nome" 
                                        size="md" />
                                    <span class="text-sm font-semibold" style="color: var(--text-light);">{{ $prof->nome }}</span>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Coletar todos os horários que têm agendamentos
                        $horariosComAgendamentos = [];
                        foreach ($agendamentos as $ag) {
                            $horaInicio = $ag->data_hora->format('H:i');
                            $duracao = $ag->servico ? $ag->servico->duracao_minutos : 30;
                            $horaFim = $ag->data_hora->copy()->addMinutes($duracao)->format('H:i');
                            
                            // Adicionar horário de início
                            if (!in_array($horaInicio, $horariosComAgendamentos)) {
                                $horariosComAgendamentos[] = $horaInicio;
                            }
                            
                            // Adicionar horários intermediários (a cada 30min)
                            $horaAtual = \Carbon\Carbon::createFromFormat('H:i', $horaInicio);
                            $horaFimObj = \Carbon\Carbon::createFromFormat('H:i', $horaFim);
                            while ($horaAtual->lt($horaFimObj)) {
                                $horaStr = $horaAtual->format('H:i');
                                if (!in_array($horaStr, $horariosComAgendamentos)) {
                                    $horariosComAgendamentos[] = $horaStr;
                                }
                                $horaAtual->addMinutes(30);
                            }
                        }
                        
                        // Se não houver agendamentos, mostrar horários padrão (9h-18h)
                        if (empty($horariosComAgendamentos)) {
                            for ($h = 9; $h < 18; $h++) {
                                $horariosComAgendamentos[] = sprintf('%02d:00', $h);
                                $horariosComAgendamentos[] = sprintf('%02d:30', $h);
                            }
                        } else {
                            // Ordenar horários
                            sort($horariosComAgendamentos);
                            
                            // Adicionar contexto: 1 hora antes do primeiro e 1 hora depois do último
                            $primeiroHorario = \Carbon\Carbon::createFromFormat('H:i', $horariosComAgendamentos[0]);
                            $ultimoHorario = \Carbon\Carbon::createFromFormat('H:i', $horariosComAgendamentos[count($horariosComAgendamentos) - 1]);
                            
                            $horariosComContexto = [];
                            // Adicionar 1 hora antes
                            $horaContexto = $primeiroHorario->copy()->subHour();
                            for ($i = 0; $i < 2; $i++) {
                                $horariosComContexto[] = $horaContexto->format('H:i');
                                $horaContexto->addMinutes(30);
                            }
                            
                            // Adicionar horários com agendamentos
                            $horariosComContexto = array_merge($horariosComContexto, $horariosComAgendamentos);
                            
                            // Adicionar 1 hora depois
                            $horaContexto = $ultimoHorario->copy()->addMinutes(30);
                            for ($i = 0; $i < 2; $i++) {
                                $horariosComContexto[] = $horaContexto->format('H:i');
                                $horaContexto->addMinutes(30);
                            }
                            
                            $horariosComAgendamentos = array_unique($horariosComContexto);
                            sort($horariosComAgendamentos);
                        }
                        
                        // Agrupar agendamentos por profissional e horário
                        $agendamentosPorProf = [];
                        foreach ($agendamentos as $ag) {
                            $profId = $ag->profissional_id;
                            if (!isset($agendamentosPorProf[$profId])) {
                                $agendamentosPorProf[$profId] = [];
                            }
                            $agendamentosPorProf[$profId][] = $ag;
                        }
                    @endphp
                    
                    @foreach($horariosComAgendamentos as $horario)
                        @php
                            // Verificar se este horário tem algum agendamento em qualquer profissional
                            $temAgendamento = false;
                            foreach ($profissionaisParaExibir as $prof) {
                                $ag = collect($agendamentosPorProf[$prof->id] ?? [])
                                    ->first(function($ag) use ($horario) {
                                        $horaAgendamento = $ag->data_hora->format('H:i');
                                        $duracao = $ag->servico ? $ag->servico->duracao_minutos : 30;
                                        $horaFim = $ag->data_hora->copy()->addMinutes($duracao)->format('H:i');
                                        
                                        // Verificar se o horário está dentro do intervalo do agendamento
                                        return $horaAgendamento <= $horario && $horario < $horaFim;
                                    });
                                if ($ag) {
                                    $temAgendamento = true;
                                    break;
                                }
                            }
                            
                            // Altura da linha: 25px se estiver vazia (mais compacta), 70px se tiver agendamento
                            $alturaLinha = $temAgendamento ? 70 : 25;
                        @endphp
                        <tr class="transition-colors" style="border-bottom:1px solid var(--brand-border);">
                            <td class="sticky left-0 z-10 px-4 py-2 text-sm font-semibold border-r" style="border-right:1px solid var(--brand-border); color: var(--text-light); background: var(--brand-surface);">
                                {{ $horario }}
                            </td>
                            @foreach($profissionaisParaExibir as $prof)
                                <td class="px-2 py-1 border-r relative" style="height: {{ $alturaLinha }}px; border-right:1px solid var(--brand-border); background: transparent;">
                                    @php
                                        // Encontrar agendamento que começa neste horário
                                        $agendamentoPrincipal = collect($agendamentosPorProf[$prof->id] ?? [])
                                            ->first(function($ag) use ($horario) {
                                                $horaAgendamento = $ag->data_hora->format('H:i');
                                                return $horaAgendamento == $horario;
                                            });
                                    @endphp
                                    
                                    @if($agendamentoPrincipal)
                                        @php
                                            $duracao = $agendamentoPrincipal->servico ? $agendamentoPrincipal->servico->duracao_minutos : 30;
                                            // Calcular quantos slots de 30min o agendamento ocupa
                                            $slots = max(1, ceil($duracao / 30));
                                            
                                            // Calcular altura: considerar que linhas vazias têm 25px e linhas com agendamento têm 70px
                                            // Mas para simplificar, vamos usar 70px por slot quando há agendamento
                                            $alturaTotal = ($slots * 70) - 4; // 70px por slot, menos 4px de margem
                                            $coresStatus = match($agendamentoPrincipal->status) {
                                                'concluido' => [
                                                    'bg' => 'bg-green-600',
                                                    'text' => 'text-white',
                                                    'border' => 'border-green-700'
                                                ],
                                                'pre_concluido' => [
                                                    'bg' => 'bg-orange-500',
                                                    'text' => 'text-white',
                                                    'border' => 'border-orange-600'
                                                ],
                                                'agendado' => [
                                                    'bg' => 'bg-blue-600',
                                                    'text' => 'text-white',
                                                    'border' => 'border-blue-700'
                                                ],
                                                default => [
                                                    'bg' => 'bg-gray-500',
                                                    'text' => 'text-white',
                                                    'border' => 'border-gray-600'
                                                ]
                                            };
                                        @endphp
                                        <div class="absolute top-1 left-1 right-1 rounded-lg shadow-sm p-2 overflow-hidden transition hover:scale-105" 
                                             style="height: {{ $alturaTotal }}px; z-index: 5; background: var(--brand-secondary); color: var(--brand-on-secondary); border-left: 3px solid rgba(0,0,0,0.1);">
                                            <div class="flex flex-col h-full justify-between">
                                                <div>
                                                    <div class="font-bold text-sm truncate">{{ $agendamentoPrincipal->nome_cliente }}</div>
                                                    @if($agendamentoPrincipal->servico)
                                                        <div class="text-xs opacity-90 truncate">{{ $agendamentoPrincipal->servico->nome }}</div>
                                                    @endif
                                                </div>
                                                <div class="flex items-center justify-between mt-1">
                                                    <div class="text-xs font-medium">{{ $agendamentoPrincipal->data_hora->format('H:i') }}</div>
                                                    <div class="text-xs opacity-75">{{ $duracao }}min</div>
                                                </div>
                                                <!-- BOTÕES SIMPLES E DIRETOS -->
                                                <div style="margin-top: 8px; display: flex; gap: 4px;">
                                                    <button onclick="window.location.href='{{ route('agendamentos.mostrar-finalizar', $agendamentoPrincipal) }}'" 
                                                            style="background: #10b981; color: white; border: none; padding: 6px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; cursor: pointer;">
                                                        FINALIZAR
                                                    </button>
                                                    <button onclick="if(confirm('Cancelar?')) { window.location.href='{{ route('agendamentos.cancelar', $agendamentoPrincipal) }}'; }" 
                                                            style="background: #ef4444; color: white; border: none; padding: 6px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; cursor: pointer;">
                                                        CANCELAR
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legenda -->
    <div class="mt-4 rounded-lg p-4" style="background: var(--brand-surface); border: 1px solid var(--brand-border);">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm font-semibold" style="color: var(--text-light);">Legenda:</span>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4" style="background: var(--brand-primary); border-radius:6px;"></div>
                <span class="text-sm" style="color: var(--text-light);">Agendado</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4" style="background: var(--brand-warning); border-radius:6px;"></div>
                <span class="text-sm" style="color: var(--text-light);">Pré-Concluído</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4" style="background: var(--brand-success); border-radius:6px;"></div>
                <span class="text-sm" style="color: var(--text-light);">Concluído</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4" style="background: var(--brand-muted); border-radius:6px;"></div>
                <span class="text-sm" style="color: var(--text-light);">Cancelado</span>
            </div>
        </div>
    </div>
</div>

<style>
    /* Scrollbar personalizada */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: var(--brand-secondary);
        border-radius: 4px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: var(--brand-secondary);
    }
    
    /* Esconder atributos onclick visualmente */
    .cursor-pointer[onclick] {
        /* Não mostrar o onclick como texto */
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript carregado na agenda!');
    
    // Tornar agendamentos clicáveis para todos (temporário para teste)
    // @if(auth()->user()->isProprietaria() || auth()->user()->isAdmin())
    const user = auth()->user();
    if (true) { // Forçar para todos testarem
        // Adicionar botões simples nos agendamentos
        document.querySelectorAll('td.relative').forEach(function(cell) {
            const agendamentoDiv = cell.querySelector('div.absolute');
            if (!agendamentoDiv) return;
            
            // Extrair ID do agendamento do conteúdo
            const content = agendamentoDiv.textContent || agendamentoDiv.innerText;
            const agendamentoMatch = content.match(/ID:\s*(\d+)/);
            const agendamentoId = agendamentoMatch ? agendamentoMatch[1] : null;
            
            if (!agendamentoId) return;
            
            console.log('Encontrado agendamento ID:', agendamentoId);
            
            // Adicionar botão de teste visível
            const testBtn = document.createElement('button');
            testBtn.innerHTML = 'TESTE';
            testBtn.style.cssText = `
                position: absolute;
                top: 2px;
                left: 2px;
                background: red;
                color: white;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 10px;
                font-weight: bold;
                z-index: 100;
            `;
            testBtn.onclick = function() {
                alert('Botão teste clicado! Agendamento ID: ' + agendamentoId);
            };
            agendamentoDiv.appendChild(testBtn);
            
            // Verificar status pela cor do elemento
            const status = agendamentoDiv.style.background.includes('green') ? 'concluido' : 
                          agendamentoDiv.style.background.includes('orange') ? 'pre_concluido' : 'agendado';
            
            // Criar container para botões
            const buttonContainer = document.createElement('div');
            buttonContainer.style.cssText = `
                position: absolute;
                bottom: 4px;
                left: 4px;
                right: 4px;
                display: flex;
                gap: 4px;
                z-index: 20;
            `;
            
            // Botão Finalizar
            if (status !== 'concluido' && status !== 'cancelado') {
                const btnFinalizar = document.createElement('button');
                btnFinalizar.innerHTML = 'Finalizar';
                btnFinalizar.style.cssText = `
                    flex: 1;
                    background: #10b981;
                    color: white;
                    border: none;
                    padding: 2px 4px;
                    border-radius: 3px;
                    font-size: 10px;
                    font-weight: bold;
                    cursor: pointer;
                `;
                btnFinalizar.onclick = function(e) {
                    e.stopPropagation();
                    window.location.href = '/agendamentos/' + agendamentoId + '/faturar';
                };
                buttonContainer.appendChild(btnFinalizar);
            }
            
            // Botão Cancelar
            if (status !== 'concluido' && status !== 'cancelado') {
                const btnCancelar = document.createElement('button');
                btnCancelar.innerHTML = 'Cancelar';
                btnCancelar.style.cssText = `
                    flex: 1;
                    background: #ef4444;
                    color: white;
                    border: none;
                    padding: 2px 4px;
                    border-radius: 3px;
                    font-size: 10px;
                    font-weight: bold;
                    cursor: pointer;
                `;
                btnCancelar.onclick = function(e) {
                    e.stopPropagation();
                    if (confirm('Deseja cancelar este agendamento?')) {
                        window.location.href = '/agendamentos/' + agendamentoId + '/cancelar';
                    }
                };
                buttonContainer.appendChild(btnCancelar);
            }
            
            // Adicionar botões ao agendamento
            agendamentoDiv.style.position = 'relative';
            agendamentoDiv.appendChild(buttonContainer);
        });
    }
    @endif
});

function removerAgendamento(id, event) {
    event.stopPropagation();
    if(confirm('Tem certeza que deseja remover permanentemente este agendamento?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/agendamentos/' + id + '/deletar';
        const csrfToken = document.querySelector('meta[name=csrf-token]').getAttribute('content');
        form.innerHTML = '<input type=hidden name=_method value=DELETE><input type=hidden name=_token value=' + csrfToken + '>';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
