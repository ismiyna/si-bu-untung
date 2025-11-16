<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $table = 'staff';
    protected $primaryKey = 'id_staff';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_staff',
        'username',
        'email',
        'password',
        'role',
        'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    // ⬇️ Inilah cast untuk kolom tanggal
    protected $casts = [
        'last_login_at' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function canAccessFilament(): bool
    {
        return in_array($this->role, ['owner','manager','karyawan']);
    }

    public function getNameAttribute(): string
    {
        return $this->username ?? $this->email ?? 'Staff';
    }
}
