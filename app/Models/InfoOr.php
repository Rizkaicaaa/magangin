<?php
// Model InfoOr
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
    public function jadwalSeleksi()
    {
        return $this->hasMany(JadwalSeleksi::class);
    }

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function templateSertifikat()
    {
        return $this->hasMany(TemplateSertifikat::class);
    }
}