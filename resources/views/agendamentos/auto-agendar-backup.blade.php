@extends('layouts.cliente')

@section('title', 'Agendar Atendimento')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="shadow-lg mb-6 rounded-lg site-header" style="background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary)); color: var(--brand-header-text);">
        <div class="p-6 flex items-center gap-6">
            <div class="shrink-0">
                <x-site-logo class="h-16 w-auto" />
            </div>
            <div>
                <h1 class="text-3xl font-bold" style="color: var(--brand-header-text);">Agendar Atendimento</h1>
                <p class="font-semibold mt-1" style="color: var(--brand-header-text); opacity:0.95">Escolha o profissional, serviço e horário desejado</p>
            </div>
        </div>
    </div>

    @php $soloMode = \App\Models\Setting::get('site.solo_mode'); @endphp

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

    <form method="POST" action="{{ route('agendamentos.store-auto') }}" id="form-agendamento" class="grid lg:grid-cols-3 gap-6 items-start" novalidate>
        @csrf

        <div class="lg:col-span-2 bg-brand-surface rounded-lg  shadow-sm" style="border:1px solid var(--brand-border);">
            <div class="wizard-progress mb-4 flex items-center gap-3">
                <div class="flex-1 h-2 bg-gray-200 rounded overflow-hidden">
                    <div id="wizard-progress-bar" class="h-2 bg-brand-primary w-1/4"></div>
                </div>
                <div class="text-sm text-gray-500 ml-3" id="wizard-step-label">Passo 1 de 4</div>
            </div>

            <div class="space-y-6 relative">
                <!-- Step 1: Profissional + Serviço -->
                <section class="step" data-step="1">
                        @php $defaultProf = $profissionais->first(); @endphp
                        @if($soloMode)
                            <input type="hidden" name="profissional_id" value="{{ old('profissional_id', auth()->user()->profissional->id ?? $defaultProf->id ?? '') }}">
                        @else
                            <label class="block text-base font-bold text-gray-900 mb-3">Escolha o Profissional *</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="profissionais-container">
                                @foreach($profissionais as $prof)
                                    <label class="relative cursor-pointer profissional-tile">
                                        <input type="radio" name="profissional_id" value="{{ $prof->id }}" 
                                               {{ old('profissional_id') == $prof->id ? 'checked' : '' }}
                                               class="peer sr-only" required>
                                        <div class="flex flex-col items-center gap-3 p-4 border-2 rounded-lg peer-checked:shadow-lg hover:shadow-md transition-all profissional-card" 
                                             style="border-color: var(--brand-border); background-color: var(--brand-bg); color: var(--text-light);">
                                            <x-avatar :src="$prof->avatar_url" :name="$prof->nome" size="lg" />
                                            <div class="text-center">
                                                <div class="font-bold text-lg">{{ $prof->nome }}</div>
                                                <div class="text-sm font-medium">{{ $prof->percentual_comissao }}% comissão</div>
                                            </div>
                                            <div class="peer-checked:block hidden">
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" style="color: var(--brand-on-secondary);">
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
                        @endif

                    <div class="mt-6">
                        <label class="block text-base font-bold text-gray-900 mb-2">Serviço *</label>
                        <input type="hidden" name="servico_id" id="servico_id_hidden" value="{{ old('servico_id') }}">
                        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4" id="servicos-catalogo">
                            @foreach($servicos as $serv)
                                <label class="cursor-pointer servico-tile" data-id="{{ $serv->id }}" data-preco="{{ $serv->preco ?? 0 }}">
                                    <input type="radio" name="servico_radio" value="{{ $serv->id }}" class="sr-only" {{ old('servico_id') == $serv->id ? 'checked' : '' }}>
                                    <div class="p-4 rounded-lg border-2 hover:shadow-md transition transform hover:-translate-y-1" style="border-color:var(--brand-border); background:var(--brand-bg); color:var(--text-light);">
                                        <div class="servico-image bg-gray-100 rounded-md mb-3 overflow-hidden flex items-center justify-center">
                                            @if(isset($serv->imagem_url) && $serv->imagem_url)
                                                <img src="{{ $serv->imagem_url }}" alt="{{ $serv->nome }}">
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h4l3 10h8l3-14H6L3 7z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="font-semibold text-lg">{{ $serv->nome }}</div>
                                                @if(!empty($serv->duracao_minutos))
                                                    <div class="text-sm text-muted">{{ $serv->duracao_minutos }} min</div>
                                                @endif
                                            </div>
                                            <div class="font-bold">R$ {{ number_format($serv->preco ?? 0,2,',','.') }}</div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('servico_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </section>

                <!-- Step 2: Data e Horário -->
                <section class="step hidden" data-step="2">
                    <input type="hidden" name="hora" id="hora" value="{{ old('hora') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-base font-bold text-gray-900 mb-2">Escolha o Dia *</label>
                            <input type="hidden" name="data" id="data" value="{{ old('data') }}">
                            <div id="date-chips" class="flex gap-2 overflow-auto py-2"></div>
                            @error('data')<p class="text-red-600 text-sm font-semibold mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-base font-bold text-gray-900 mb-2">Horários disponíveis *</label>
                            <div id="timeslots" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2"></div>
                            <div id="sugestoes-container" class="mt-3 hidden bg-orange-50 border-2 border-orange-300 rounded-lg p-3">
                                <p class="text-sm font-bold text-gray-900 mb-2">Horário não disponível. Sugestões:</p>
                                <div id="sugestoes-buttons" class="flex flex-wrap gap-2"></div>
                            </div>

                            <!-- Status do horário -->
                            <div id="status-horario" class="mt-2 text-sm font-bold"></div>
                        </div>
                    </div>
                </section>

                <!-- Step 3: Dados do cliente -->
                <section class="step hidden" data-step="3">
                    <div>
                        <label class="block text-base font-bold text-gray-900 mb-2">Seu Nome *</label>
                        <input type="text" name="cliente_avulso" value="{{ old('cliente_avulso', auth()->user()->name ?? '') }}" required
                            class="block w-full rounded-lg border-2 bg-brand-bg shadow-sm focus:border-brand-secondary focus:ring-brand-secondary text-base py-3 px-4 font-medium" style="border-color:var(--brand-border); color:var(--text-light); background:var(--brand-bg);">
                        @error('cliente_avulso')
                            <p class="text-red-600 text-sm font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label class="block text-base font-bold text-gray-900 mb-2">Observações (opcional)</label>
                        <textarea name="observacoes" rows="3"
                                  class="block w-full rounded-lg border-2 bg-brand-bg shadow-sm focus:border-brand-secondary focus:ring-brand-secondary text-base py-3 px-4 font-medium" style="border-color:var(--brand-border); color:var(--text-light); background:var(--brand-bg);">{{ old('observacoes') }}</textarea>
                        @error('observacoes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </section>

                <!-- Step 4: Revisão e submit -->
                <section class="step hidden" data-step="4">
                    <h3 class="text-lg font-semibold">Revisar Agendamento</h3>
                    <div class="mt-3 space-y-2">
                        <div><span class="text-xs text-gray-500">Profissional:</span> <span id="review-prof">—</span></div>
                        <div><span class="text-xs text-gray-500">Serviço:</span> <span id="review-serv">—</span></div>
                        <div><span class="text-xs text-gray-500">Data:</span> <span id="review-date">—</span></div>
                        <div><span class="text-xs text-gray-500">Horário:</span> <span id="review-time">—</span></div>
                        <div><span class="text-xs text-gray-500">Preço:</span> <span id="review-price">R$ 0,00</span></div>
                    </div>
                </section>

                <!-- Navegação do wizard -->
                <div class="mt-6 flex items-center gap-3 justify-between">
                    <button type="button" id="wizard-back" class="px-4 py-2 rounded-lg bg-transparent border" style="border-color:var(--brand-border); color:var(--text-light);">Voltar</button>
                    <div class="flex items-center gap-3">
                        <button type="button" id="wizard-next" class="px-4 py-2 rounded-lg bg-brand-primary text-white" style="background:var(--brand-primary);">Próximo</button>
                        <button type="submit" id="btn-submit-hidden" class="px-4 py-2 rounded-lg bg-brand-secondary text-white hidden">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo lateral (desktop) -->
        <aside class="hidden lg:block lg:col-span-1">
            <div class="sticky top-24 bg-brand-surface rounded-lg p-4 shadow-sm" style="border:1px solid var(--brand-border);">
                <div class="flex items-center gap-3 mb-4">
                    <x-site-logo class="h-10 w-auto" />
                </div>
                <h4 class="font-semibold">Resumo</h4>
                <div class="mt-3 text-sm text-muted">Selecione serviço, data e horário. Aqui verá o resumo antes de confirmar.</div>

                <div class="mt-4 space-y-3">
                    <div>
                        <div class="text-xs text-gray-500">Profissional</div>
                        <div id="summary-prof" class="font-medium mt-1">—</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Serviço</div>
                        <div id="summary-serv" class="font-medium mt-1">—</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Data</div>
                        <div id="summary-date" class="font-medium mt-1">—</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Horário</div>
                        <div id="summary-time" class="font-medium mt-1">—</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Preço</div>
                        <div id="summary-price" class="font-medium mt-1">R$ 0,00</div>
                    </div>
                </div>

                <div class="mt-6">
                    <a id="summary-cta" href="#" class="block text-center px-4 py-2 rounded-lg text-white font-semibold" style="background:var(--brand-secondary);">Confirmar</a>
                </div>
            </div>
        </aside>
    </form>
</div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    try {
        console.log('Inicializando sistema de agendamento...');
        const publicScheduleMode = @json($mostrarAgendaComprometida ?? false);
        const soloMode = @json($soloMode ?? false);
        console.log('Configurações:', { publicScheduleMode, soloMode });
        const profissionalSelect = document.querySelectorAll('input[name="profissional_id"]');
    const servicoHidden = document.getElementById('servico_id_hidden');
    const servicoRadios = Array.from(document.querySelectorAll('input[name="servico_radio"]'));
    const dataInput = document.getElementById('data');
    const horaInput = document.getElementById('hora');
    const dateChips = document.getElementById('date-chips');
    const timeslotsContainer = document.getElementById('timeslots');
    const sugestoesContainer = document.getElementById('sugestoes-container');
    const sugestoesButtons = document.getElementById('sugestoes-buttons');
    const statusHorario = document.getElementById('status-horario');
    const form = document.getElementById('form-agendamento');
    const summaryProf = document.getElementById('summary-prof');
    const summaryServ = document.getElementById('summary-serv');
    const summaryDate = document.getElementById('summary-date');
    const summaryTime = document.getElementById('summary-time');
    const summaryPrice = document.getElementById('summary-price');
    const summaryCta = document.getElementById('summary-cta');
    const wizardSteps = Array.from(document.querySelectorAll('.step'));
    const wizardProgressBar = document.getElementById('wizard-progress-bar');
    const wizardStepLabel = document.getElementById('wizard-step-label');
    const wizardNext = document.getElementById('wizard-next');
    const wizardBack = document.getElementById('wizard-back');
    let currentStep = 1;
    let verificando = false;

    function showStepInstant(step) {
        wizardSteps.forEach(s => s.classList.add('hidden'));
        const node = document.querySelector('.step[data-step="' + step + '"]');
        if (node) node.classList.remove('hidden');
        // progress
        const total = wizardSteps.length;
        const pct = Math.round((step / total) * 100);
        if (wizardProgressBar) wizardProgressBar.style.width = pct + '%';
        if (wizardStepLabel) wizardStepLabel.textContent = 'Passo ' + step + ' de ' + total;
        // focus first input
        const firstInput = node ? node.querySelector('input, select, textarea, button') : null;
        if (firstInput) firstInput.focus();
        // adjust buttons
        if (wizardBack) wizardBack.style.display = step === 1 ? 'none' : 'inline-block';
        if (wizardNext) wizardNext.textContent = step === total ? 'Revisar' : 'Próximo';
        currentStep = step;
    }

    function transitionToStep(target) {
        const currentNode = document.querySelector('.step:not(.hidden)');
        const targetNode = document.querySelector('.step[data-step="' + target + '"]');
        if (!targetNode) return showStepInstant(target);
        if (currentNode) {
            currentNode.classList.add('fade-out-step');
            setTimeout(() => {
                currentNode.classList.add('hidden');
                currentNode.classList.remove('fade-out-step');
                // show target
                targetNode.classList.remove('hidden');
                targetNode.classList.add('fade-in-step');
                setTimeout(() => targetNode.classList.remove('fade-in-step'), 360);
                // update progress and labels
                const total = wizardSteps.length;
                const pct = Math.round((target / total) * 100);
                if (wizardProgressBar) wizardProgressBar.style.width = pct + '%';
                if (wizardStepLabel) wizardStepLabel.textContent = 'Passo ' + target + ' de ' + total;
                if (wizardBack) wizardBack.style.display = target === 1 ? 'none' : 'inline-block';
                if (wizardNext) wizardNext.textContent = target === total ? 'Revisar' : 'Próximo';
                currentStep = target;
            }, 320);
        } else {
            // no current node visible
            targetNode.classList.remove('hidden');
            targetNode.classList.add('fade-in-step');
            setTimeout(() => targetNode.classList.remove('fade-in-step'), 360);
            currentStep = target;
        }
    }

    function updateSummary() {
        const profChecked = document.querySelector('input[name="profissional_id"]:checked');
        if (profChecked) {
            const label = profChecked.closest('label');
            const nome = label?.querySelector('.font-bold')?.textContent?.trim() || '—';
            summaryProf.textContent = nome;
        } else {
            summaryProf.textContent = '—';
        }

        const servChecked = document.querySelector('input[name="servico_radio"]:checked');
        if (servChecked) {
            const label = servChecked.closest('label');
            const nome = label?.querySelector('.font-semibold')?.textContent?.trim() || '—';
            summaryServ.textContent = nome + ' — R$ ' + (servChecked.closest('label')?.dataset?.preco ? Number(servChecked.closest('label').dataset.preco).toFixed(2).replace('.',',') : '0,00');
            summaryPrice.textContent = 'R$ ' + (servChecked.closest('label')?.dataset?.preco ? Number(servChecked.closest('label').dataset.preco).toFixed(2).replace('.',',') : '0,00');
        } else {
            summaryServ.textContent = '—';
            summaryPrice.textContent = 'R$ 0,00';
        }

        summaryDate.textContent = dataInput.value || '—';
        summaryTime.textContent = horaInput.value || '—';
    }

    function formatDateISO(d) {
        return d.toISOString().split('T')[0];
    }

    function renderDateChips(days = 14) {
        if (!dateChips) return;
        dateChips.innerHTML = '';
        const today = new Date();
        for (let i = 0; i < days; i++) {
            const d = new Date(today);
            d.setDate(today.getDate() + i);
            const iso = formatDateISO(d);
            const label = d.toLocaleDateString('pt-BR', { weekday: 'short', day: '2-digit', month: 'short' });
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'date-chip px-3 py-2 rounded-lg bg-brand-bg border';
            btn.dataset.date = iso;
            btn.innerHTML = `<div class="text-sm font-medium">${label}</div><div class="text-xs text-gray-500">${iso}</div>`;
            btn.addEventListener('click', function() {
                // mark selected
                document.querySelectorAll('.date-chip').forEach(c=>c.classList.remove('selected'));
                this.classList.add('selected');
                dataInput.value = this.dataset.date;
                updateSummary();
                // render timeslots for this date
                renderTimeSlots(this.dataset.date);
                // auto-check availability for chosen day
                // do not set hora yet
                verificarHorario();
            });
            dateChips.appendChild(btn);
        }
    }

    async function renderTimeSlots(date, from='09:00', to='17:30', stepMin=30) {
        if (!timeslotsContainer) return;
        timeslotsContainer.innerHTML = '';

        // Se estiver em solo mode, não mostrar seleção de profissionais
        if (soloMode) {
            const profissionalId = auth()->user()?.profissional?.id || document.querySelector('input[name="profissional_id"]')?.value;
            const servChecked = document.querySelector('input[name="servico_radio"]:checked');
            const servicoId = servChecked ? servChecked.value : servicoHidden.value;
            if (!profissionalId || !servicoId || !date) {
                timeslotsContainer.innerHTML = '<div class="text-sm text-gray-500">Selecione um serviço e dia para ver os horários disponíveis.</div>';
                return;
            }

            statusHorario.innerHTML = '';
            try {
                const resp = await fetch('{{ route("api.horarios-disponiveis-dia") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ profissional_id: profissionalId, servico_id: servicoId, data: date, from: from, to: to, step: stepMin })
                });
                const json = await resp.json();
                const times = json.timeslots || [];
                times.forEach(t => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'timeslot px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105';
                    btn.textContent = t.hora;
                    btn.dataset.time = t.hora;
                    
                    if (t.disponivel) {
                        btn.classList.add('bg-white', 'border-2', 'border-green-200', 'text-green-700', 'hover:bg-green-50', 'hover:border-green-400', 'hover:shadow-md');
                        btn.style.border = '2px solid #86efac';
                        btn.addEventListener('click', function() {
                            document.querySelectorAll('.timeslot').forEach(t=>t.classList.remove('selected', 'ring-2', 'ring-green-500', 'bg-green-100'));
                            this.classList.add('selected', 'ring-2', 'ring-green-500', 'bg-green-100');
                            horaInput.value = this.dataset.time;
                            updateSummary();
                            setTimeout(()=>{ transitionToStep(3); }, 220);
                        });
                    } else {
                        btn.classList.add('bg-red-50', 'border-2', 'border-red-200', 'text-red-600', 'cursor-not-allowed', 'opacity-60', 'line-through');
                        btn.style.border = '2px dashed #fca5a5';
                        btn.disabled = true;
                        btn.title = 'Horário indisponível';
                    }
                    timeslotsContainer.appendChild(btn);
                });
            } catch (e) {
                console.error(e);
                timeslotsContainer.innerHTML = '<div class="text-sm text-red-600">Erro ao carregar horários disponíveis.</div>';
            }
            return;
        }

        // Modo agenda completa (publicScheduleMode)
        if (publicScheduleMode) {
            const profissionalId = document.querySelector('input[name="profissional_id"]:checked')?.value || document.querySelector('input[name="profissional_id"]')?.value;
            const servChecked = document.querySelector('input[name="servico_radio"]:checked');
            const servicoId = servChecked ? servChecked.value : servicoHidden.value;
            if (!profissionalId || !servicoId || !date) {
                timeslotsContainer.innerHTML = '<div class="text-sm text-gray-500">Selecione profissional, serviço e dia para ver a agenda pública.</div>';
                return;
            }

            statusHorario.innerHTML = '';
            try {
                const resp = await fetch('{{ route("api.horarios-disponiveis-dia") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ profissional_id: profissionalId, servico_id: servicoId, data: date, from: from, to: to, step: stepMin })
                });
                const json = await resp.json();
                const times = json.timeslots || [];
                times.forEach(t => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'timeslot px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 transform hover:scale-105';
                    btn.textContent = t.hora;
                    btn.dataset.time = t.hora;
                    
                    if (t.disponivel) {
                        btn.classList.add('bg-white', 'border-2', 'border-green-200', 'text-green-700', 'hover:bg-green-50', 'hover:border-green-400', 'hover:shadow-md');
                        btn.style.border = '2px solid #86efac';
                        btn.addEventListener('click', function() {
                            document.querySelectorAll('.timeslot').forEach(t=>t.classList.remove('selected', 'ring-2', 'ring-green-500', 'bg-green-100'));
                            this.classList.add('selected', 'ring-2', 'ring-green-500', 'bg-green-100');
                            horaInput.value = this.dataset.time;
                            updateSummary();
                            setTimeout(()=>{ transitionToStep(3); }, 220);
                        });
                    } else {
                        btn.classList.add('bg-red-50', 'border-2', 'border-red-200', 'text-red-600', 'cursor-not-allowed', 'opacity-60', 'line-through');
                        btn.style.border = '2px dashed #fca5a5';
                        btn.disabled = true;
                        btn.title = 'Horário indisponível';
                    }
                    timeslotsContainer.appendChild(btn);
                });
            } catch (e) {
                console.error(e);
                timeslotsContainer.innerHTML = '<div class="text-sm text-red-600">Erro ao carregar a agenda pública.</div>';
            }
            return;
        }

        // Modo normal (padrão) - mostrar apenas horários disponíveis com verificação individual
        const [fromH, fromM] = from.split(':').map(Number);
        const [toH, toM] = to.split(':').map(Number);
        let cur = new Date();
        cur.setHours(fromH, fromM, 0, 0);
        const end = new Date();
        end.setHours(toH, toM, 0, 0);
        while (cur <= end) {
            const hh = String(cur.getHours()).padStart(2,'0');
            const mm = String(cur.getMinutes()).padStart(2,'0');
            const time = `${hh}:${mm}`;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'timeslot px-3 py-2 rounded-lg bg-white border-2 border-gray-200 text-sm font-medium transition-all duration-200 transform hover:scale-105 hover:bg-blue-50 hover:border-blue-400 hover:shadow-md';
            btn.textContent = time;
            btn.dataset.time = time;
            btn.addEventListener('click', function() {
                document.querySelectorAll('.timeslot').forEach(t=>t.classList.remove('selected', 'ring-2', 'ring-blue-500', 'bg-blue-100'));
                this.classList.add('selected', 'ring-2', 'ring-blue-500', 'bg-blue-100');
                horaInput.value = this.dataset.time;
                updateSummary();
                verificarHorario();
                setTimeout(()=>{ transitionToStep(3); }, 350);
            });
            timeslotsContainer.appendChild(btn);
            cur.setMinutes(cur.getMinutes() + stepMin);
        }
    }

    function verificarHorario() {
        updateSummary();
        const profissionalId = document.querySelector('input[name="profissional_id"]:checked')?.value;
        const servChecked = document.querySelector('input[name="servico_radio"]:checked');
        const servicoId = servChecked ? servChecked.value : servicoHidden.value;
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
                        btn.style.backgroundColor = 'var(--brand-secondary)';
                        btn.style.color = 'var(--brand-on-secondary)';
                        btn.onmouseover = function() { this.style.filter = 'brightness(0.95)'; };
                        btn.onmouseout = function() { this.style.filter = 'none'; };
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
        radio.addEventListener('change', () => { 
            console.log('Profissional selecionado:', radio.value);
            verificarHorario(); 
            updateSummary(); 
        });
    });
    // listeners for servico selection (tiles)
    // keep change listeners for keyboard accessibility
    servicoRadios.forEach(r => {
        r.addEventListener('change', function() {
            servicoHidden.value = this.value;
            verificarHorario();
            updateSummary();
        });
    });

    // delegate clicks on the catalog container for robustness
    const servicosCatalog = document.getElementById('servicos-catalogo');
    if (servicosCatalog) {
        servicosCatalog.addEventListener('click', function(e) {
            console.log('Clique detectado no catálogo de serviços');
            const label = e.target.closest('label.servico-tile');
            if (!label) {
                console.log('Label não encontrado');
                return;
            }
            const radio = label.querySelector('input[name="servico_radio"]');
            if (!radio) {
                console.log('Radio não encontrado');
                return;
            }
            console.log('Serviço selecionado:', radio.value);
            // select radio and sync
            radio.checked = true;
            servicoHidden.value = radio.value;
            verificarHorario();
            updateSummary();
            // animate catalog out and show next step
            console.log('Iniciando animação para próximo passo');
            servicosCatalog.classList.add('fade-out');
            setTimeout(() => {
                servicosCatalog.classList.remove('fade-out');
                renderDateChips(14);
                renderTimeSlots(new Date().toISOString().split('T')[0]);
                transitionToStep(2);
            }, 320);
        });
    }
    dataInput.addEventListener('change', () => { verificarHorario(); updateSummary(); });
    horaInput.addEventListener('change', () => { verificarHorario(); updateSummary(); });
    horaInput.addEventListener('input', function() {
        // Verificar após 1 segundo de inatividade
        clearTimeout(window.verificarTimeout);
        window.verificarTimeout = setTimeout(verificarHorario, 1000);
        updateSummary();
    });

    // Wizard navigation
    if (wizardNext) {
        wizardNext.addEventListener('click', function() {
            const total = wizardSteps.length;
            if (currentStep < total) {
                const next = currentStep + 1;
                // if entering final (review) step, populate review after transition
                if (next === total) {
                    document.getElementById('review-prof').textContent = summaryProf.textContent;
                    document.getElementById('review-serv').textContent = summaryServ.textContent;
                    document.getElementById('review-date').textContent = summaryDate.textContent;
                    document.getElementById('review-time').textContent = summaryTime.textContent;
                    document.getElementById('review-price').textContent = summaryPrice.textContent;
                }
                transitionToStep(next);
            } else {
                // already at final step -> submit
                document.getElementById('btn-submit-hidden').classList.remove('hidden');
                document.getElementById('btn-submit-hidden').click();
            }
        });
    }

    if (wizardBack) {
        wizardBack.addEventListener('click', function() {
            if (currentStep > 1) {
                const prev = currentStep - 1;
                transitionToStep(prev);
            }
        });
    }

    // summary CTA -> submit (lateral)
    if (summaryCta) {
        summaryCta.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('btn-submit-hidden').classList.remove('hidden');
            document.getElementById('btn-submit-hidden').click();
        });
    }

    // Inicializar resumo e wizard
    updateSummary();
    showStepInstant(currentStep);
});
</script>

<style>
    /* Estilos para cards de profissionais (mais específicos para evitar !important) */
    form#form-agendamento label.profissional-tile input[name="profissional_id"]:checked + div {
        border-color: var(--brand-secondary);
        background-color: var(--brand-secondary);
        color: var(--brand-on-secondary);
    }

    form#form-agendamento label.servico-tile input[name="servico_radio"]:checked + div {
        border-color: var(--brand-secondary);
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        transform: translateY(-4px);
    }

    /* Date chips and timeslot styles (clean, responsive, no !important) */
    #date-chips { 
        padding-bottom: 6px; 
        display: flex; 
        gap: 0.5rem; 
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }
    
    .date-chip {
        min-width: 110px;
        max-width: 120px;
        flex-shrink: 0;
        border: 1px solid var(--brand-border);
        background: var(--brand-bg);
        color: var(--text-light);
        padding: 0.5rem 0.75rem;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        gap: 4px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
        cursor: pointer;
        position: relative;
        z-index: 1;
    }
    .date-chip:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .date-chip.selected {
        background: var(--brand-secondary);
        color: var(--brand-on-secondary);
        border-color: var(--brand-secondary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Enhanced timeslot styles */
    .timeslot {
        position: relative;
        overflow: hidden;
        font-weight: 500;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        box-sizing: border-box;
        margin: 0;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    
    .timeslot:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .timeslot:hover:before {
        left: 100%;
    }

    .timeslot:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.04); }
    .timeslot.selected {
        background: var(--brand-secondary);
        color: var(--brand-on-secondary);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transform: translateY(-4px);
    }
    .timeslot[disabled], .timeslot.disabled {
        opacity: 0.45;
        cursor: not-allowed;
        border-style: dashed;
        transform: none;
        box-shadow: none;
    }

    /* Container dos timeslots - CORREÇÃO DE SOBREPOSIÇÃO */
    #timeslots {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 8px !important;
        width: 100% !important;
        box-sizing: border-box !important;
        margin: 0 !important;
        padding: 0 !important;
        clear: both !important;
        overflow: hidden !important;
    }

    /* Garantir espaçamento entre seções */
    .grid.grid-cols-1.md\\:grid-cols-2.gap-4 > div:nth-child(2) {
        margin-top: 1rem;
    }

    .servico-image img { width:100%; height:100%; object-fit:cover; display:block; }
    .servico-image { aspect-ratio: 1 / 1; }

    form#form-agendamento label.profissional-tile input[name="profissional_id"]:checked + div svg {
        display: block;
        color: var(--brand-on-secondary);
    }

    /* Melhorar contraste dos inputs sem usar !important */
    form#form-agendamento input[type="date"],
    form#form-agendamento input[type="time"],
    form#form-agendamento input[type="text"],
    form#form-agendamento select,
    form#form-agendamento textarea {
        background-color: var(--brand-bg);
        border-color: var(--brand-border);
        color: var(--text-light);
    }

    form#form-agendamento input[type="date"]:focus,
    form#form-agendamento input[type="time"]:focus,
    form#form-agendamento input[type="text"]:focus,
    form#form-agendamento select:focus,
    form#form-agendamento textarea:focus {
        border-color: var(--brand-secondary);
        outline: none;
        box-shadow: 0 6px 18px rgba(59,130,246,0.08);
    }

    /* Cards de profissionais com hover */
    form#form-agendamento label.profissional-tile:hover > div {
        border-color: var(--brand-secondary);
        box-shadow: 0 6px 18px rgba(16,185,129,0.06);
    }

    .profissional-card { transition: box-shadow .18s ease, transform .12s ease; }
    .profissional-card:hover { transform: translateY(-2px); }

    /* CSS responsivo para corrigir sobreposição */
    @media (max-width: 640px) {
        #timeslots {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 6px !important;
        }
        
        .timeslot {
            font-size: 13px !important;
            padding: 8px 4px !important;
            min-height: 40px !important;
        }
        
        .date-chip {
            min-width: 90px !important;
            padding: 0.4rem 0.6rem !important;
            font-size: 12px !important;
        }
    }

    @media (max-width: 480px) {
        #timeslots {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 4px !important;
        }
        
        .timeslot {
            font-size: 12px !important;
            padding: 6px 2px !important;
            min-height: 36px !important;
        }
    }

    /* Wizard animations */
    .step { transition: transform .32s cubic-bezier(.2,.9,.2,1), opacity .24s ease; }
    .step.hidden { opacity: 0; transform: translateY(8px); height: 0; overflow: hidden; }
    .wizard-progress { align-items: center }
    .fade-out { opacity: 0; transform: scale(.98); transition: opacity .32s ease, transform .32s ease; }
    .fade-in { opacity: 1; transform: none; transition: opacity .32s ease, transform .32s ease; }
    .fade-out-step { opacity: 0; transform: translateY(-8px); transition: all .32s ease; }
    .fade-in-step { opacity: 0; transform: translateY(8px); animation: enterStep .32s forwards; }
    @keyframes enterStep { to { opacity: 1; transform: none; } }
    @media (prefers-reduced-motion: reduce) {
        .step { transition: none; }
    }
</style>

    });
    } catch (error) {
        console.error('Erro crítico no JavaScript:', error);
        alert('Ocorreu um erro ao carregar o sistema de agendamento. Por favor, recarregue a página.');
    }
    });
</script>
@endsection

