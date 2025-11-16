<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // agar bisa dipakai untuk auth guard 'pelanggan'
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class Pelanggan extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';

    // PK string manual seperti "PLG000001"
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pelanggan',   // diisi otomatis pada booted()
        'nama_pelanggan',
        'alamat',
        'no_hp',
        'username',
        'email',
        'password',
        'last_login_at',  // ⬅️ tambahkan agar bisa diisi saat login
    ];

    protected $hidden = ['password', 'remember_token'];

    // ⬇️ Cast tanggal agar otomatis jadi instance Carbon saat diakses
    protected $casts = [
        'last_login_at' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    /**
     * Mutator: otomatis hash password jika masih plaintext.
     */
    public function setPasswordAttribute($value): void
    {
        // Jika sudah berbentuk bcrypt ($2y$...), jangan di-hash ulang
        $this->attributes['password'] = is_string($value) && str_starts_with($value, '$2y$')
            ? $value
            : Hash::make($value);
    }

    /**
     * Hook: saat creating, generate ID pelanggan "PLG + 6 digit" (PLG000001, dst).
     * Pastikan kolom id_pelanggan VARCHAR(12) dan FK tabel anak juga VARCHAR(12).
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (!empty($model->id_pelanggan)) {
                return; // hormati ID yang sudah diisi manual (jika ada)
            }

            $prefix = 'PLG';
            $digits = 6; // total panjang 3 + 6 = 9 char (cukup dalam VARCHAR(12))

            // Ambil nomor terbesar yang sudah ada (SUBSTRING mulai dari char ke-4 setelah 'PLG')
            $last = DB::table('pelanggan')
                ->where('id_pelanggan', 'like', $prefix . '%')
                ->selectRaw('COALESCE(MAX(CAST(SUBSTRING(id_pelanggan, 4) AS UNSIGNED)), 0) AS max_num')
                ->value('max_num');

            $nextNumber = ((int) $last) + 1;
            $model->id_pelanggan = $prefix . str_pad((string) $nextNumber, $digits, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Relasi: satu pelanggan punya banyak transaksi.
     */
    public function transaksi()
    {
        // Jika model Transaksi ada di App\Models\Transaksi, ini sudah benar.
        // Jika namespace-nya berbeda, sesuaikan.
        return $this->hasMany(Transaksi::class, 'id_pelanggan', 'id_pelanggan');
    }
}
