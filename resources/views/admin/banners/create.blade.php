@extends('layouts.app')

@section('title', 'Novo Banner')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Criar Novo Banner</h1>
        <p class="mt-2 text-gray-600">Adicione imagens responsivas para desktop e mobile</p>
    </div>

    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-8">
        @csrf

        <!-- Informações Básicas -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 mr-3">1</span>
                Informações Básicas
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Título (Opcional)</label>
                    <input type="text" name="titulo" value="{{ old('titulo') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ex: Promoção de Verão">
                    @error('titulo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ordem de Exibição</label>
                    <input type="number" name="ordem" value="{{ old('ordem', 0) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="0">
                    @error('ordem')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Descrição (Opcional)</label>
                <textarea name="descricao" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Descreva o banner...">{{ old('descricao') }}</textarea>
                @error('descricao')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Link de Destino (Opcional)</label>
                <input type="url" name="link_destino" value="{{ old('link_destino') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="https://exemplo.com">
                @error('link_destino')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Separador -->
        <hr class="my-8">

        <!-- Banner Desktop -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 mr-3">2</span>
                Banner Desktop (1920x400px)
            </h2>

            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition" id="dropZoneDesktop">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
                </svg>
                <input type="file" name="banner_desktop" accept="image/*" id="banner_desktop"
                       class="hidden" onchange="previewBanner(this, 'previewDesktop')">
                <label for="banner_desktop" class="cursor-pointer">
                    <span class="text-gray-700 font-medium">Clique para upload ou arraste uma imagem</span>
                    <p class="text-sm text-gray-500 mt-1">PNG, JPG, GIF, WEBP (máx. 5MB)</p>
                    <p class="text-xs text-gray-400 mt-2">Recomendado: 1920x400px para melhor qualidade</p>
                </label>
                <div id="previewDesktop" class="mt-4"></div>
            </div>
            @error('banner_desktop')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Banner Mobile -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 mr-3">3</span>
                Banner Mobile (540x960px)
            </h2>

            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition" id="dropZoneMobile">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
                </svg>
                <input type="file" name="banner_mobile" accept="image/*" id="banner_mobile"
                       class="hidden" onchange="previewBanner(this, 'previewMobile')">
                <label for="banner_mobile" class="cursor-pointer">
                    <span class="text-gray-700 font-medium">Clique para upload ou arraste uma imagem</span>
                    <p class="text-sm text-gray-500 mt-1">PNG, JPG, GIF, WEBP (máx. 5MB)</p>
                    <p class="text-xs text-gray-400 mt-2">Recomendado: 540x960px para melhor qualidade</p>
                </label>
                <div id="previewMobile" class="mt-4"></div>
            </div>
            @error('banner_mobile')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status -->
        <div class="mb-8">
            <label class="flex items-center">
                <input type="hidden" name="ativo" value="0">
                <input type="checkbox" name="ativo" value="1" {{ old('ativo', 1) ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                <span class="ml-2 text-sm font-medium text-gray-700">Ativar este banner imediatamente</span>
            </label>
        </div>

        <!-- Botões -->
        <div class="flex gap-4">
            <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Criar Banner
            </button>
            <a href="{{ route('admin.banners.index') }}" class="flex-1 px-6 py-3 bg-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-400 transition text-center">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
    function previewBanner(input, previewId) {
        const preview = document.getElementById(previewId);
        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'max-w-full h-auto rounded-lg border border-gray-200 mx-auto';
                
                const container = document.createElement('div');
                container.className = 'relative inline-block';
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700';
                removeBtn.innerHTML = '✕';
                removeBtn.onclick = function(e) {
                    e.preventDefault();
                    input.value = '';
                    preview.innerHTML = '';
                };
                
                container.appendChild(img);
                container.appendChild(removeBtn);
                preview.appendChild(container);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    // Drag and drop
    ['dropZoneDesktop', 'dropZoneMobile'].forEach(zoneId => {
        const zone = document.getElementById(zoneId);
        const inputId = zoneId === 'dropZoneDesktop' ? 'banner_desktop' : 'banner_mobile';
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            zone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            zone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            zone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            zone.classList.add('border-blue-500', 'bg-blue-50');
        }

        function unhighlight(e) {
            zone.classList.remove('border-blue-500', 'bg-blue-50');
        }

        zone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            const input = document.getElementById(inputId);
            input.files = files;
            previewBanner(input, inputId === 'banner_desktop' ? 'previewDesktop' : 'previewMobile');
        }
    });
</script>
@endsection
