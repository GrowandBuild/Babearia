<!-- Company Name -->
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Nome da Empresa</label>
    <input type="text" 
           name="company_name" 
           value="{{ \App\Models\Setting::get('company_name') }}" 
           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
           placeholder="Digite o nome da empresa">
    @error('company_name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
    <p class="text-xs text-gray-500 mt-1">Este nome será usado no aplicativo e em todos os lugares onde "Vida Maria" aparece.</p>
</div>

<!-- Logo Info -->
<div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <p class="text-sm text-blue-800 font-medium">Logo da Empresa</p>
            <p class="text-xs text-blue-600 mt-1">O logo usado no aplicativo é o mesmo configurado em "Aparência > Logo do Site".</p>
        </div>
    </div>
</div>
