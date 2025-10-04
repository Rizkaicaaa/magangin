<?php

// Model JadwalSeleksi
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalSeleksi extends Model
{
    use HasFactory;

    protected $table = 'jadwal_seleksi';

    protected $fillable = [
        'info_or_id',
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

    // Relationships
    public function infoOr()
    {
        return $this->belongsTo(InfoOr::class);
    }

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class);
    }
    
    public function mahasiswas()
{
    return $this->belongsToMany(User::class, 'jadwal_mahasiswa', 'jadwal_id', 'mahasiswa_id');
}

}