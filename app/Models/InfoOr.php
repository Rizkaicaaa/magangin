<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoOr extends Model
{
    use HasFactory;

    protected $table = 'info_or';

    protected $fillable = [
        'judul',
        'deskripsi',
        'persyaratan_umum',
        'tanggal_buka',
        'tanggal_tutup',
        'periode',
        'gambar',
        'status',
    ];

    protected $casts = [
        'tanggal_buka' => 'date',
        'tanggal_tutup' => 'date',
    ];

    // Relationships
    
    /**
     * Relasi ke JadwalKegiatan (One to Many)
     * Satu periode memiliki banyak jadwal kegiatan
     */
    public function jadwalKegiatan()
    {
        return $this->hasMany(JadwalKegiatan::class, 'info_or_id');
    }
    
    /**
     * Relasi ke JadwalSeleksi (One to Many)
     */
    public function jadwalSeleksi()
    {
        return $this->hasMany(JadwalSeleksi::class, 'info_or_id');
    }
    
    /**
     * Relasi ke Pendaftaran (One to Many)
     */
    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'info_or_id');
    }
    
    /**
     * Relasi ke Template Sertifikat (One to Many)
     */
    public function templateSertifikat()
    {
        return $this->hasMany(TemplateSertifikat::class, 'info_or_id');
    }
}