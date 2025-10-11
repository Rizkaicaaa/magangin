<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalSeleksi extends Model
{
    use HasFactory;

    protected $table = 'jadwal_seleksi';

    protected $fillable = [
        'info_or_id',
        'pendaftaran_id',
        'tanggal_seleksi',
        'waktu_mulai',
        'waktu_selesai',
        'tempat',
        'pewawancara',
    ];

    protected $casts = [
        'tanggal_seleksi' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    // Relasi ke Info OR
    public function infoOr()
    {
        return $this->belongsTo(InfoOr::class);
    }

    // Relasi ke Pendaftaran (satu jadwal untuk satu pendaftaran)
    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }

    // Relasi ke Mahasiswa (many-to-many)
    public function mahasiswas()
    {
        return $this->belongsToMany(User::class, 'jadwal_mahasiswa', 'jadwal_id', 'mahasiswa_id');
    }

    public function pendaftarans()
{
    return $this->belongsToMany(Pendaftaran::class, 'jadwal_pendaftaran', 'jadwal_id', 'pendaftaran_id');
}

}
