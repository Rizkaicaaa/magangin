<?php

// Model Pendaftaran
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran';

    protected $fillable = [
        'user_id',
        'info_or_id',
        'jadwal_seleksi_id',
        'pilihan_dinas_1',
        'pilihan_dinas_2',
        'motivasi',
        'pengalaman',
        'file_cv',
        'file_transkrip',
        'status_pendaftaran',
        'dinas_diterima_id',
        'tanggal_daftar',
    ];

    protected $casts = [
        'tanggal_daftar' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function infoOr()
    {
        return $this->belongsTo(InfoOr::class);
    }

    public function jadwalSeleksi()
    {
        return $this->belongsTo(JadwalSeleksi::class);
    }

    public function dinasPilihan1()
    {
        return $this->belongsTo(Dinas::class, 'pilihan_dinas_1');
    }

    public function dinasPilihan2()
    {
        return $this->belongsTo(Dinas::class, 'pilihan_dinas_2');
    }

    public function dinasDiterima()
    {
        return $this->belongsTo(Dinas::class, 'dinas_diterima_id');
    }

    public function penilaianWawancara()
    {
        return $this->hasOne(PenilaianWawancara::class);
    }

    public function evaluasiMagang()
    {
        return $this->hasOne(EvaluasiMagang::class);
    }
}
