<div>
    <h3 class="text-lg font-medium text-gray-900">Identidade do Site</h3>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-4">
        @csrf
        @method('PATCH')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Logo do site</label>
            <div class="mt-2 flex items-center">
                @if(\App\Models\Setting::get('site.logo'))
                    <img src="{{ asset('storage/' . \App\Models\Setting::get('site.logo')) }}" alt="Logo" class="h-16 w-auto mr-4" />
                @endif
                <input type="file" name="site_logo" accept="image/*" />
            </div>
            @if(\App\Models\Setting::get('site.logo'))
                <label class="inline-flex items-center mt-2">
                    <input type="checkbox" name="remove_site_logo" value="1" class="mr-2"> Remover logo atual
                </label>
            @endif
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Banner do Agendamento</label>
            <div class="mt-2">
                @if(\App\Models\Setting::get('site.banner'))
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . \App\Models\Setting::get('site.banner')) }}" alt="Banner" class="w-full h-16 object-cover rounded-lg shadow-sm" />
                        <p class="text-xs text-gray-500 mt-1">Banner atual (arquivo)</p>
                    </div>
                @endif
                @if(\App\Models\Setting::get('site.banner_url'))
                    <div class="mb-4">
                        <img src="{{ \App\Models\Setting::get('site.banner_url') }}" alt="Banner URL" class="w-full h-16 object-cover rounded-lg shadow-sm" />
                        <p class="text-xs text-gray-500 mt-1">Banner atual (URL)</p>
                    </div>
                @endif
                <input type="file" name="site_banner" accept="image/*" class="w-full mb-3" />
                <p class="text-sm text-gray-500 mb-3">Recomendado: 1920x400px. O texto terá overlay escuro automático para garantir legibilidade.</p>
                
                <label class="block text-sm font-medium text-gray-700 mb-2">OU adicionar via URL:</label>
                <input type="url" 
                       name="site_banner_url" 
                       value="{{ \App\Models\Setting::get('site.banner_url') }}" 
                       placeholder="https://exemplo.com/banner.jpg" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <p class="text-sm text-gray-500 mt-1">URL de uma imagem externa. Será priorizada sobre o arquivo se ambos estiverem configurados.</p>
            </div>
            <div class="mt-3 space-x-4">
                @if(\App\Models\Setting::get('site.banner'))
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remove_site_banner" value="1" class="mr-2"> Remover banner (arquivo)
                    </label>
                @endif
                @if(\App\Models\Setting::get('site.banner_url'))
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remove_site_banner_url" value="1" class="mr-2"> Remover banner (URL)
                    </label>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Cor Primária</label>
                <input type="color" name="primary_color" value="{{ \App\Models\Setting::get('brand.primary', '#1f2937') }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Cor Secundária</label>
                <input type="color" name="secondary_color" value="{{ \App\Models\Setting::get('brand.secondary', '#3b82f6') }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Cor Terciária</label>
                <input type="color" name="tertiary_color" value="{{ \App\Models\Setting::get('brand.tertiary', '#10b981') }}" class="mt-2 h-10 w-full p-0" />
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Cor do Texto (modo claro)</label>
                <input type="color" name="text_color_light" value="{{ \App\Models\Setting::get('brand.text_light', '#111827') }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Cor do Texto (modo escuro)</label>
                <input type="color" name="text_color_dark" value="{{ \App\Models\Setting::get('brand.text_dark', '#f9fafb') }}" class="mt-2 h-10 w-full p-0" />
            </div>
        </div>

        <div class="mt-4">
            <div class="flex items-center space-x-4">
                <div class="p-3 rounded" style="background: {{ \App\Models\Setting::get('brand.primary', '#1f2937') }}; width:48px; height:48px"></div>
                <div class="p-3 rounded" style="background: {{ \App\Models\Setting::get('brand.secondary', '#3b82f6') }}; width:48px; height:48px"></div>
                <div class="p-3 rounded" style="background: {{ \App\Models\Setting::get('brand.tertiary', '#10b981') }}; width:48px; height:48px"></div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Accent</label>
                <input type="color" name="accent_color" value="{{ \App\Models\Setting::get('brand.accent', '#f97316') }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Background</label>
                <input type="color" name="bg_color" value="{{ \App\Models\Setting::get('brand.bg', '#ffffff') }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Surface</label>
                <input type="color" name="surface_color" value="{{ \App\Models\Setting::get('brand.surface', '#f8fafc') }}" class="mt-2 h-10 w-full p-0" />
            </div>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Border</label>
                <input type="color" name="border_color" value="{{ \App\Models\Setting::get('brand.border', '#e5e7eb') }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Muted</label>
                <input type="color" name="muted_color" value="{{ \App\Models\Setting::get('brand.muted', '#6b7280') }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">On Primary (texto sobre primary)</label>
                <input type="color" name="text_on_primary" value="{{ \App\Models\Setting::get('brand.on_primary', '#ffffff') }}" class="mt-2 h-10 w-full p-0" />
            </div>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Success</label>
                <input type="color" name="success_color" value="{{ \App\Models\Setting::get('brand.success', '#10b981') }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Warning</label>
                <input type="color" name="warning_color" value="{{ \App\Models\Setting::get('brand.warning', '#f59e0b') }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Danger</label>
                <input type="color" name="danger_color" value="{{ \App\Models\Setting::get('brand.danger', '#ef4444') }}" class="mt-2 h-10 w-full p-0" />
            </div>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Info</label>
                <input type="color" name="info_color" value="{{ \App\Models\Setting::get('brand.info', '#3b82f6') }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">On Secondary (texto sobre secondary)</label>
                <input type="color" name="text_on_secondary" value="{{ \App\Models\Setting::get('brand.on_secondary', '#ffffff') }}" class="mt-2 h-10 w-full p-0" />
            </div>
        </div>

        <hr class="my-6" />
        <h4 class="text-md font-semibold mb-3">Header e Botões</h4>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Header - Fundo</label>
                <input type="color" name="header_bg" value="{{ \App\Models\Setting::get('brand.header_bg', \App\Models\Setting::get('brand.primary', '#1f2937')) }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Header - Texto</label>
                <input type="color" name="header_text" value="{{ \App\Models\Setting::get('brand.header_text', \App\Models\Setting::get('brand.on_primary', '#ffffff')) }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div class="flex items-end">
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700">Preview</label>
                    <div class="mt-2 p-3 rounded border border-gray-200" style="background: {{ \App\Models\Setting::get('brand.header_bg', \App\Models\Setting::get('brand.primary', '#1f2937')) }}; color: {{ \App\Models\Setting::get('brand.header_text', \App\Models\Setting::get('brand.on_primary', '#ffffff')) }}">
                        <strong>Header Preview</strong> — Vida Maria
                    </div>
                </div>
            </div>
        <div class="mt-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="public_schedule_mode" value="1" class="mr-2" {{ \App\Models\Setting::get('site.public_schedule_mode') ? 'checked' : '' }}>
                <span class="text-sm text-gray-700">Mostrar agenda pública — exibir horários disponíveis abertamente no processo de agendamento</span>
            </label>
            <p class="text-xs text-gray-500 mt-1">Quando marcado, os clientes verão a agenda completa e poderão escolher horários diretamente. Quando desmarcado, o sistema só sugerirá horários disponíveis (modo sugestão).</p>
        </div>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Botão Primário - Fundo</label>
                <input type="color" name="btn_primary_bg" value="{{ \App\Models\Setting::get('brand.btn_primary_bg', \App\Models\Setting::get('brand.primary', '#1f2937')) }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Botão Primário - Texto</label>
                <input type="color" name="btn_primary_text" value="{{ \App\Models\Setting::get('brand.btn_primary_text', \App\Models\Setting::get('brand.on_primary', '#ffffff')) }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Botão Primário - Hover</label>
                <input type="color" name="btn_primary_hover" value="{{ \App\Models\Setting::get('brand.btn_primary_hover', \App\Models\Setting::get('brand.btn_primary_bg', \App\Models\Setting::get('brand.primary', '#1f2937'))) }}" class="mt-2 h-10 w-full p-0" />
            </div>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Botão Secundário - Fundo</label>
                <input type="color" name="btn_secondary_bg" value="{{ \App\Models\Setting::get('brand.btn_secondary_bg', \App\Models\Setting::get('brand.secondary', '#3b82f6')) }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Botão Secundário - Texto</label>
                <input type="color" name="btn_secondary_text" value="{{ \App\Models\Setting::get('brand.btn_secondary_text', \App\Models\Setting::get('brand.on_secondary', '#ffffff')) }}" class="mt-2 h-10 w-full p-0" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Botão Secundário - Hover</label>
                <input type="color" name="btn_secondary_hover" value="{{ \App\Models\Setting::get('brand.btn_secondary_hover', \App\Models\Setting::get('brand.btn_secondary_bg', \App\Models\Setting::get('brand.secondary', '#3b82f6'))) }}" class="mt-2 h-10 w-full p-0" />
            </div>
        </div>

        <div class="mt-4 flex items-center gap-4">
            <button type="button" class="px-4 py-2 rounded btn-primary-preview" style="background: {{ \App\Models\Setting::get('brand.btn_primary_bg', \App\Models\Setting::get('brand.primary', '#1f2937')) }}; color: {{ \App\Models\Setting::get('brand.btn_primary_text', \App\Models\Setting::get('brand.on_primary', '#ffffff')) }}">Primário</button>
            <button type="button" class="px-4 py-2 rounded btn-secondary-preview" style="background: {{ \App\Models\Setting::get('brand.btn_secondary_bg', \App\Models\Setting::get('brand.secondary', '#3b82f6')) }}; color: {{ \App\Models\Setting::get('brand.btn_secondary_text', \App\Models\Setting::get('brand.on_secondary', '#ffffff')) }}">Secundário</button>
        </div>
        <div class="mt-6">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">Salvar identidade</button>
        </div>
        
        <hr class="my-6" />
        <h4 class="text-md font-semibold mb-3">Configurações de Funcionamento</h4>
        <div class="mt-2">
            <label class="inline-flex items-center">
                <input type="checkbox" name="solo_mode" value="1" class="mr-2" {{ \App\Models\Setting::get('site.solo_mode') ? 'checked' : '' }}>
                <span class="text-sm text-gray-700">Trabalho sozinho — desabilitar seleção de profissionais (modo colaboradores OFF)</span>
            </label>
            <p class="text-xs text-gray-500 mt-1">Quando marcado, os usuários não precisarão selecionar um profissional ao agendar. Útil para estabelecimentos com apenas uma pessoa.</p>
        </div>
    </form>
</div>
