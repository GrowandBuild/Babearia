<!-- Cor Primária do Cliente -->
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Cor Principal (Botões e Destaques)</label>
    <div class="flex items-center space-x-3">
        <input type="color" 
               name="client_primary_color" 
               value="{{ \App\Models\Setting::get('client.primary_color', '#3b82f6') }}"
               class="h-10 w-20 rounded border-gray-300 cursor-pointer">
        <input type="text" 
               name="client_primary_color_text" 
               value="{{ \App\Models\Setting::get('client.primary_color', '#3b82f6') }}"
               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
               placeholder="#3b82f6">
    </div>
    <p class="text-xs text-gray-500 mt-1">Cor dos botões principais e elementos de destaque.</p>
</div>

<!-- Tema do Modal -->
<div class="mt-4">
    <label class="block text-sm font-medium text-gray-700 mb-2">Tema do Modal</label>
    <div class="space-y-2">
        <label class="flex items-center">
            <input type="radio" name="client_modal_theme" value="light" 
                   @if(\App\Models\Setting::get('client.modal_theme', 'light') === 'light') checked @endif
                   class="mr-2 border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm">🌞 Claro</span>
        </label>
        <label class="flex items-center">
            <input type="radio" name="client_modal_theme" value="dark" 
                   @if(\App\Models\Setting::get('client.modal_theme', 'light') === 'dark') checked @endif
                   class="mr-2 border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm">🌙 Escuro</span>
        </label>
    </div>
    <p class="text-xs text-gray-500 mt-1">Define se os modais (janelas pop-up) serão claros ou escuros.</p>
</div>

<!-- Preview Visual -->
<div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
    <p class="text-xs font-medium text-gray-700 mb-2">🎨 Preview Visual</p>
    <div id="client-preview" class="p-3 rounded border" style="border-color: {{ \App\Models\Setting::get('client.primary_color', '#3b82f6') }};">
        <button class="px-3 py-1 rounded text-white text-xs" style="background-color: {{ \App\Models\Setting::get('client.primary_color', '#3b82f6') }};">
            Botão Principal
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sincronizar color picker com text input
    function syncColorInputs(colorInput, textInput) {
        colorInput.addEventListener('input', function() {
            textInput.value = this.value;
            updatePreview();
        });
        
        textInput.addEventListener('input', function() {
            if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                colorInput.value = this.value;
                updatePreview();
            }
        });
    }
    
    syncColorInputs(
        document.querySelector('input[name="client_primary_color"]'),
        document.querySelector('input[name="client_primary_color_text"]')
    );
    
    function updatePreview() {
        const preview = document.getElementById('client-preview');
        const primaryColor = document.querySelector('input[name="client_primary_color"]').value;
        
        preview.style.borderColor = primaryColor;
        preview.querySelector('button').style.backgroundColor = primaryColor;
    }
});
</script>
