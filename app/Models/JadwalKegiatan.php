<?php

// Model JadwalKegiatan
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKegiatan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_kegiatan';

    protected $fillable = [
        'dinas_id',
        'nama_kegiatan',
        'deskripsi_kegiatan',
        'tanggal_kegiatan',
        'waktu_mulai',
        'waktu_selesai',
        'tempat',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    // Relationships
    public function dinas()
    {
        return $this->belongsTo(Dinas::class);
    }
}
