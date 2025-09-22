<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilSeleksi extends Model
{
    use HasFactory;

    // karena nama tabel ada spasi, sebaiknya pakai underscore di database
    // pastikan nama tabel kamu adalah 'hasil_seleksi'
    protected $table = 'hasil_seleksi'; 

    protected $primaryKey = 'ID_Hasil_Seleksi'; 

    public $timestamps = false; // karena tabel kamu tidak ada created_at & updated_at

    protected $fillable = [
        'ID_Nilai_Wawancara',
        'Nilai_Total',
        'Status_Seleksi',
    ];
}
