<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKegiatan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_kegiatan';

    protected $fillable = [
        'info_or_id', // Sesuaikan dengan migration
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
    
    /**
     * Relasi ke InfoOr (Many to One)
     * Satu jadwal kegiatan belongs to satu periode (InfoOr)
     */
    public function infoOr()
    {
        return $this->belongsTo(InfoOr::class, 'info_or_id');
    }
    
    /**
     * Accessor untuk mendapatkan periode dari InfoOr
     */
    public function getPeriodeAttribute()
    {
        return $this->infoOr?->periode;
    }
    
    /**
     * Scope untuk filter berdasarkan periode
     */
    public function scopeByPeriode($query, $periodeId)
    {
        return $query->where('info_or_id', $periodeId);
    }
    
    /**
     * Scope untuk kegiatan aktif (berdasarkan periode yang buka)
     */
    public function scopeAktif($query)
    {
        return $query->whereHas('infoOr', function ($q) {
            $q->where('status', 'buka');
        });
    }
}