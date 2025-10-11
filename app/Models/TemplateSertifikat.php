<?php

// Model TemplateSertifikat
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateSertifikat extends Model
{
    use HasFactory;

    protected $table = 'template_sertifikat';

    protected $fillable = [
        'info_or_id',
        'nama_template',
        'file_template',
        'status',
    ];

    protected $casts = [
        'placeholder_fields' => 'array',
    ];

    // Relationships
    public function infoOr()
    {
        return $this->belongsTo(InfoOr::class);
    }

    public function evaluasiMagang()
    {
        return $this->hasMany(EvaluasiMagang::class);
    }
}
