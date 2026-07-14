<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'titulo',
        'banner_desktop',
        'banner_mobile',
        'descricao',
        'link_destino',
        'ativo',
        'ordem',
        'mostrar_titulo',
        'mostrar_descricao',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'mostrar_titulo' => 'boolean',
        'mostrar_descricao' => 'boolean',
    ];

    /**
     * Obter o banner ativo atual
     */
    public static function getAtivo()
    {
        return self::where('ativo', true)
            ->orderBy('ordem')
            ->first();
    }

    /**
     * Obter URL do banner baseado no dispositivo
     */
    public function getUrlBanner($isMobile = false)
    {
        if ($isMobile && $this->banner_mobile) {
            return asset('storage/' . $this->banner_mobile);
        }
        
        if ($this->banner_desktop) {
            return asset('storage/' . $this->banner_desktop);
        }
        
        return null;
    }
}
