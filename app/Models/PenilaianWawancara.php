<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianWawancara extends Model
{
    use HasFactory;

    protected $table = 'penilaian_wawancara';

    protected $fillable = [
        'pendaftaran_id',
        'penilai_id',
        'jadwal_seleksi_id',
        'nilai_komunikasi',
        'nilai_motivasi',
        'nilai_kemampuan',
        'nilai_total',
        'nilai_rata_rata',
        'status',
        'kkm',
    ];


    protected $casts = [
        'nilai_komunikasi' => 'integer',
        'nilai_motivasi' => 'integer',
        'nilai_kemampuan' => 'integer',
        'nilai_total' => 'integer',
        'kkm' => 'integer',
        'nilai_rata_rata' => 'decimal:2',
    ];

    // Relasi ke peserta
    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    // Relasi ke jadwal seleksi
    public function jadwal()
    {
        return $this->belongsTo(JadwalSeleksi::class, 'jadwal_seleksi_id');
    }

    // Relasi ke user penilai
    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }

    // Accessor untuk pewawancara
    public function getPewawancaraAttribute($value)
    {
        return $value ?? $this->jadwal?->pewawancara ?? '-';
    }

    public function getNamaPesertaAttribute()
    {
        return $this->pendaftaran?->user?->nama_lengkap ?? '-';
    }
}