<?php

// Model User
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'role',
        'nama_lengkap',
        'nim',
        'no_telp',
        'tanggal_daftar',
        'status',
        'dinas_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tanggal_daftar' => 'date',
        'password' => 'hashed',
    ];

    // Relationships
    public function dinas()
    {
        return $this->belongsTo(Dinas::class);
    }

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function penilaianWawancaraAsPenilai()
    {
        return $this->hasMany(PenilaianWawancara::class, 'penilai_id');
    }

    public function evaluasiMagangAsPenilai()
    {
        return $this->hasMany(EvaluasiMagangModel::class, 'penilai_id');
    }
}