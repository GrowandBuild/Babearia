<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::orderBy('ordem')->paginate(10);
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'nullable|string|max:255',
            'banner_desktop' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'banner_mobile' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'descricao' => 'nullable|string',
            'link_destino' => 'nullable|url',
            'ativo' => 'boolean',
            'ordem' => 'integer|min:0',
        ]);

        $data = $request->only(['titulo', 'descricao', 'link_destino', 'ativo', 'ordem']);

        // Upload banner desktop
        if ($request->hasFile('banner_desktop')) {
            $data['banner_desktop'] = $request->file('banner_desktop')
                ->store('banners/desktop', 'public');
        }

        // Upload banner mobile
        if ($request->hasFile('banner_mobile')) {
            $data['banner_mobile'] = $request->file('banner_mobile')
                ->store('banners/mobile', 'public');
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner criado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'titulo' => 'nullable|string|max:255',
            'banner_desktop' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'banner_mobile' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'descricao' => 'nullable|string',
            'link_destino' => 'nullable|url',
            'ativo' => 'boolean',
            'ordem' => 'integer|min:0',
        ]);

        $data = $request->only(['titulo', 'descricao', 'link_destino', 'ativo', 'ordem']);

        // Upload nova imagem desktop
        if ($request->hasFile('banner_desktop')) {
            // Deletar imagem antiga
            if ($banner->banner_desktop && Storage::disk('public')->exists($banner->banner_desktop)) {
                Storage::disk('public')->delete($banner->banner_desktop);
            }
            $data['banner_desktop'] = $request->file('banner_desktop')
                ->store('banners/desktop', 'public');
        }

        // Upload nova imagem mobile
        if ($request->hasFile('banner_mobile')) {
            // Deletar imagem antiga
            if ($banner->banner_mobile && Storage::disk('public')->exists($banner->banner_mobile)) {
                Storage::disk('public')->delete($banner->banner_mobile);
            }
            $data['banner_mobile'] = $request->file('banner_mobile')
                ->store('banners/mobile', 'public');
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        // Deletar imagens
        if ($banner->banner_desktop && Storage::disk('public')->exists($banner->banner_desktop)) {
            Storage::disk('public')->delete($banner->banner_desktop);
        }
        if ($banner->banner_mobile && Storage::disk('public')->exists($banner->banner_mobile)) {
            Storage::disk('public')->delete($banner->banner_mobile);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner deletado com sucesso!');
    }

    /**
     * Toggle status do banner
     */
    public function toggle(Banner $banner)
    {
        $banner->update(['ativo' => !$banner->ativo]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Status atualizado!');
    }
}
