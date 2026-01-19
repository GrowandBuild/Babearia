<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServicoController extends Controller
{
    public function index()
    {
        $servicos = Servico::all();
        return view('servicos.index', compact('servicos'));
    }

    public function create()
    {
        return view('servicos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'duracao_minutos' => 'nullable|integer|min:0',
            'descricao' => 'nullable|string',
            'imagem_url' => 'nullable|url',
            'imagem_upload' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();
        
        // Processar upload de imagem
        if ($request->hasFile('imagem_upload')) {
            $image = $request->file('imagem_upload');
            $imageName = 'servicos/' . Str::uuid() . '.' . $image->getClientOriginalExtension();
            $image->storePubliclyAs('servicos', $image->getClientOriginalName(), 'public');
            $data['imagem_url'] = Storage::url($imageName);
        }
        
        // Se não houver upload mas tiver URL, usar a URL
        if (empty($data['imagem_url']) && $request->filled('imagem_url')) {
            $data['imagem_url'] = $request->input('imagem_url');
        }
        
        // Remover o campo de upload do array de dados
        unset($data['imagem_upload']);

        Servico::create($data);

        // Sincronizar com o seeder
        $this->syncServicesToSeeder();

        return redirect()->route('servicos.index')
            ->with('success', 'Serviço cadastrado com sucesso!');
    }

    public function edit(Servico $servico)
    {
        return view('servicos.edit', compact('servico'));
    }

    public function update(Request $request, Servico $servico)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'duracao_minutos' => 'nullable|integer|min:0',
            'descricao' => 'nullable|string',
            'imagem_url' => 'nullable|url',
            'imagem_upload' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();
        
        // Processar upload de imagem
        if ($request->hasFile('imagem_upload')) {
            $image = $request->file('imagem_upload');
            $imageName = 'servicos/' . Str::uuid() . '.' . $image->getClientOriginalExtension();
            $image->storePubliclyAs('servicos', $image->getClientOriginalName(), 'public');
            $data['imagem_url'] = Storage::url($imageName);
        }
        
        // Se não houver upload mas tiver URL, usar a URL
        if (empty($data['imagem_url']) && $request->filled('imagem_url')) {
            $data['imagem_url'] = $request->input('imagem_url');
        }
        
        // Remover o campo de upload do array de dados
        unset($data['imagem_upload']);

        $servico->update($data);

        // Sincronizar com o seeder
        $this->syncServicesToSeeder();

        return redirect()->route('servicos.index')
            ->with('success', 'Serviço atualizado com sucesso!');
    }

    public function destroy(Servico $servico)
    {
        $servico->delete();
        
        return redirect()->route('servicos.index')
            ->with('success', 'Serviço removido com sucesso!');
    }

    public function toggleStatus(Servico $servico)
    {
        $servico->update(['ativo' => !$servico->ativo]);
        
        // Sincronizar com o seeder
        $this->syncServicesToSeeder();
        
        return redirect()->route('servicos.index')
            ->with('success', 'Status atualizado com sucesso!');
    }

    private function syncServicesToSeeder()
    {
        try {
            Artisan::call('services:sync-seeder');
        } catch (\Exception $e) {
            // Log do erro mas não interrompe o fluxo
            \Log::warning('Erro ao sincronizar serviços com seeder: ' . $e->getMessage());
        }
    }
}

