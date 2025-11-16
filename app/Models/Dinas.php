<?php

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
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships dengan Pendaftaran
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

    // Relationship dengan User (jika ada user yang assigned ke dinas)
    public function users()
    {
        return $this->hasMany(User::class, 'dinas_id');
    }

    // Method untuk menghitung jumlah pendaftar pilihan 1
    public function getTotalPendaftarPilihan1Attribute()
    {
        return $this->pendaftaranPilihan1->count();
    }

    // Method untuk menghitung jumlah pendaftar pilihan 2
    public function getTotalPendaftarPilihan2Attribute()
    {
        return $this->pendaftaranPilihan2->count();
    }

    // Method untuk menghitung total pendaftar (pilihan 1 + pilihan 2)
    public function getTotalPendaftarAttribute()
    {
        return $this->pendaftaranPilihan1->count() + $this->pendaftaranPilihan2->count();
    }

    // Method untuk menghitung jumlah yang diterima
    public function getTotalDiterimaAttribute()
    {
        return $this->pendaftaranDiterima->count();
    }
}