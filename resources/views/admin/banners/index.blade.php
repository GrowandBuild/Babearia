@extends('layouts.app')

@section('title', 'Gerenciar Banners')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gerenciar Banners</h1>
                <p class="mt-2 text-gray-600">Crie e gerencie banners responsivos para a página de agendamento</p>
            </div>
            <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Novo Banner
            </a>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ $message }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($banners->count() > 0)
        <div class="grid grid-cols-1 gap-6">
            @foreach ($banners as $banner)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 p-6">
                        <!-- Preview Desktop -->
                        <div class="lg:col-span-1">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Desktop (1920x400px)</h3>
                            <div class="relative bg-gray-100 rounded-lg overflow-hidden border-2 border-gray-200" style="aspect-ratio: 16/4;">
                                @if ($banner->banner_desktop)
                                    <img src="{{ asset('storage/' . $banner->banner_desktop) }}" alt="Desktop" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Preview Mobile -->
                        <div class="lg:col-span-1">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Mobile (540x960px)</h3>
                            <div class="relative bg-gray-100 rounded-lg overflow-hidden border-2 border-gray-200 mx-auto" style="width: 180px; aspect-ratio: 9/16;">
                                @if ($banner->banner_mobile)
                                    <img src="{{ asset('storage/' . $banner->banner_mobile) }}" alt="Mobile" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Informações -->
                        <div class="lg:col-span-1">
                            <div class="space-y-3">
                                @if ($banner->titulo)
                                    <div>
                                        <span class="text-sm font-semibold text-gray-700">Título:</span>
                                        <p class="text-sm text-gray-600">{{ $banner->titulo }}</p>
                                    </div>
                                @endif
                                @if ($banner->descricao)
                                    <div>
                                        <span class="text-sm font-semibold text-gray-700">Descrição:</span>
                                        <p class="text-sm text-gray-600 line-clamp-2">{{ $banner->descricao }}</p>
                                    </div>
                                @endif
                                <div>
                                    <span class="text-sm font-semibold text-gray-700">Ordem:</span>
                                    <p class="text-sm text-gray-600">{{ $banner->ordem }}</p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium @if($banner->ativo) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                                        @if ($banner->ativo)
                                            ✓ Ativo
                                        @else
                                            ✗ Inativo
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Ações -->
                        <div class="lg:col-span-1 flex flex-col justify-between">
                            <div class="space-y-2">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="w-full block text-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-sm">
                                    Editar
                                </a>
                                <form action="{{ route('admin.banners.toggle', $banner) }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 @if($banner->ativo) bg-yellow-600 hover:bg-yellow-700 @else bg-green-600 hover:bg-green-700 @endif text-white font-semibold rounded-lg transition text-sm">
                                        @if ($banner->ativo)
                                            Desativar
                                        @else
                                            Ativar
                                        @endif
                                    </button>
                                </form>
                            </div>
                            <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar este banner?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition text-sm">
                                    Deletar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginação -->
        <div class="mt-8">
            {{ $banners->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhum banner cadastrado</h3>
            <p class="text-gray-600 mb-6">Comece criando seu primeiro banner responsivo</p>
            <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Criar Primeiro Banner
            </a>
        </div>
    @endif
</div>
@endsection
