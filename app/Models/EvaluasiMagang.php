<?php

// Model EvaluasiMagang
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiMagang extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_magang';

    protected $fillable = [
        'pendaftaran_id',
        'penilai_id',
        'template_sertifikat_id',
        'nilai_kedisiplinan',
        'nilai_kerjasama',
        'nilai_inisiatif',
        'nilai_hasil_kerja',
        'nilai_total',
        'nomor_sertifikat',
        'file_sertifikat',
    ];

    protected $casts = [
        'nilai_kedisiplinan' => 'decimal:2',
        'nilai_kerjasama' => 'decimal:2',
        'nilai_inisiatif' => 'decimal:2',
        'nilai_hasil_kerja' => 'decimal:2',
        'nilai_total' => 'decimal:2',
    ];

    // Relationships
    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }

    public function templateSertifikat()
    {
        return $this->belongsTo(TemplateSertifikat::class);
    }
}