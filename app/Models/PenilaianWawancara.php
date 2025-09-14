<?php


// Model PenilaianWawancara
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
        'nilai_komunikasi',
        'nilai_motivasi',
        'nilai_kemampuan',
        'nilai_total',
        'hasil',
        'status',
    ];

    protected $casts = [
        'nilai_komunikasi' => 'decimal:2',
        'nilai_motivasi' => 'decimal:2',
        'nilai_kemampuan' => 'decimal:2',
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
}