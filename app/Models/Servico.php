<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;

    protected $table = 'servicos';

    protected $fillable = [
        'nome',
        'preco',
        'duracao_minutos',
        'descricao',
        'imagem_url',
        'ativo',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function getImagemUrlAttribute($value)
    {
        if (! $value) {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        $value = preg_replace('#^storage/#', '', $value);
        $value = preg_replace('#^public/#', '', $value);

        return \Illuminate\Support\Facades\Storage::disk('public')->url($value);
    }

    // Relacionamentos
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    // Scope para serviços ativos
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }
}

