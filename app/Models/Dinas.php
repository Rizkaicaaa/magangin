<?php

// Model Dinas
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dinas extends Model
{
    use HasFactory;

    protected $table = 'dinas';

    protected $fillable = [
        'nama_dinas',
        'deskripsi',
        'kontak_person',
        'kuota_magang',
        'status',
    ];

    protected $casts = [
        'kuota_magang' => 'integer',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function pendaftaranPilihan1()
    {
        return $this->hasMany(Pendaftaran::class, 'pilihan_dinas_1');
    }

    public function pendaftaranPilihan2()
    {
        return $this->hasMany(Pendaftaran::class, 'pilihan_dinas_2');
    }

    public function pendaftaranDiterima()
    {
        return $this->hasMany(Pendaftaran::class, 'dinas_diterima_id');
    }

    public function jadwalKegiatan()
    {
        return $this->hasMany(JadwalKegiatan::class);
    }
}