<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'pegawai_id',
        'name',
        'username',
        'email',
        'foto_profil',
        'password',
        'role',
        'last_login_at',
        'last_login_ip',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === \App\Enums\UserRole::ADMIN->value;
    }

    public function isKades(): bool
    {
        return $this->role === \App\Enums\UserRole::KEPALA_DESA->value;
    }

    public function isAuditor(): bool
    {
        return $this->role === \App\Enums\UserRole::AUDITOR->value;
    }

    public function isPerangkat(): bool
    {
        return in_array($this->role, [\App\Enums\UserRole::PERANGKAT->value, \App\Enums\UserRole::STAF->value]);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'kepala_desa' => 'Kepala Desa',
            'perangkat' => 'Perangkat Desa',
            'auditor' => 'Auditor',
            'staf' => 'Staf Desa',
            default => ucfirst(str_replace('_', ' ', $this->role ?? 'User')),
        };
    }

    public function pengajuanAbsenLuars(): HasMany
    {
        return $this->hasMany(PengajuanAbsenLuar::class);
    }
}
