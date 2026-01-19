<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'phone',
        'email',
        'password',
        'tipo',
        'avatar',
        'mostrar_agenda_comprometida',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mostrar_agenda_comprometida' => 'boolean',
    ];

    // Relacionamentos
    public function profissional()
    {
        return $this->hasOne(Profissional::class);
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class);
    }

    // Helpers
    public function isProprietaria()
    {
        return $this->tipo === 'proprietaria';
    }

    public function isProfissional()
    {
        return $this->tipo === 'profissional';
    }

    public function isCliente()
    {
        return $this->tipo === 'cliente';
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            // Adicionar cache busting para forçar atualização
            return asset('storage/' . $this->avatar) . '?v=' . time();
        }
        
        // Avatar padrão com iniciais (cores premium)
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=0A1647&background=D4AF37&bold=true&size=200';
    }

}
