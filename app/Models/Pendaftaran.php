<?php

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function infoOr()
    {
        return $this->belongsTo(InfoOr::class, 'info_or_id');
    }

    public function jadwalSeleksi()
    {
        return $this->belongsTo(JadwalSeleksi::class, 'jadwal_seleksi_id');
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
        return $this->hasOne(PenilaianWawancara::class, 'pendaftaran_id');
    }

    public function evaluasiMagang()
    {
        return $this->hasOne(EvaluasiMagang::class, 'pendaftaran_id');
    }

    public function jadwals()
{
    return $this->belongsToMany(JadwalSeleksi::class, 'jadwal_pendaftaran', 'pendaftaran_id', 'jadwal_id');
}

}
