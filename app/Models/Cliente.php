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
        'instagram',
        'avatar',
        'observacoes',
        'is_package_client',
        'package_total_services',
        'package_used_services',
        'package_price',
        'package_start_date',
        'package_end_date',
        'package_observations',
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

    // Métodos para gerenciar pacotes
    public function isPackageClient()
    {
        return $this->is_package_client;
    }

    public function getRemainingServices()
    {
        if (!$this->isPackageClient()) {
            return 0;
        }
        
        return max(0, $this->package_total_services - $this->package_used_services);
    }

    public function hasPackageServices()
    {
        return $this->isPackageClient() && $this->getRemainingServices() > 0;
    }

    public function usePackageService()
    {
        if ($this->hasPackageServices()) {
            $this->increment('package_used_services');
            return true;
        }
        return false;
    }

    public function getPackageStatusAttribute()
    {
        if (!$this->isPackageClient()) {
            return 'Cliente Comum';
        }

        $remaining = $this->getRemainingServices();
        
        if ($remaining <= 0) {
            return 'Pacote Esgotado';
        } elseif ($this->package_end_date && now()->greaterThan($this->package_end_date)) {
            return 'Pacote Expirado';
        } else {
            return "{$remaining} de {$this->package_total_services} restantes";
        }
    }

    // Verificar se pacote está válido
    public function isPackageValid()
    {
        if (!$this->isPackageClient()) {
            return false;
        }

        $hasRemainingServices = $this->getRemainingServices() > 0;
        $notExpired = !$this->package_end_date || now()->lessThanOrEqualTo($this->package_end_date);
        
        return $hasRemainingServices && $notExpired;
    }
}

