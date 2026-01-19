<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'user_id',
        'nome',
        'telefone',
        'email',
        'avatar',
        'observacoes',
    ];

    // Relacionamentos
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor para URL do avatar
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return \Storage::url($this->avatar);
        }
        
        // Avatar padrão com UI Avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nome) . '&color=0A1647&background=D4AF37&bold=true&size=200';
    }

    // Calcular total gasto pelo cliente
    public function totalGasto()
    {
        $agendamentos = $this->agendamentos()
            ->where('status', 'concluido')
            ->with('pagamentos')
            ->get();
        
        $total = 0;
        foreach ($agendamentos as $agendamento) {
            foreach ($agendamento->pagamentos as $pagamento) {
                $total += $pagamento->valor;
            }
        }

        return $total;
    }

    // Calcular lucro gerado pelo cliente (valor da empresa)
    public function lucroGerado()
    {
        $agendamentos = $this->agendamentos()
            ->where('status', 'concluido')
            ->with('pagamentos')
            ->get();
        
        $total = 0;
        foreach ($agendamentos as $agendamento) {
            foreach ($agendamento->pagamentos as $pagamento) {
                $total += $pagamento->valor_empresa;
            }
        }

        return $total;
    }
}

