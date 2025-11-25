<?php

namespace Tests\Unit\Models;

use App\Models\EvaluasiMagangModel;
use App\Models\Pendaftaran;
use App\Models\TemplateSertifikatModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

// Tidak menggunakan RefreshDatabase karena kita tidak berinteraksi dengan DB
class EvaluasiMagangTest extends TestCase
{
    /** @var array Daftar kolom yang diharapkan ada di fillable */
    protected $fillableFields = [
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

    /** @var array Daftar kolom yang diharapkan di-cast */
    protected $castFields = [
        'nilai_kedisiplinan' => 'decimal:2',
        'nilai_kerjasama' => 'decimal:2',
        'nilai_inisiatif' => 'decimal:2',
        'nilai_hasil_kerja' => 'decimal:2',
        'nilai_total' => 'decimal:2',
    ];

    #[Test]
    public function it_uses_the_correct_table_name()
    {
        $model = new EvaluasiMagangModel();
        $this->assertEquals('evaluasi_magang', $model->getTable());
    }

    #[Test]
    public function it_has_the_expected_fillable_fields()
    {
        $model = new EvaluasiMagangModel();
        $this->assertEquals($this->fillableFields, $model->getFillable());
    }

    #[Test]
    public function it_casts_the_nilai_fields_to_decimal_two()
    {
        $model = new EvaluasiMagangModel();
        $casts = $model->getCasts();

        foreach ($this->castFields as $field => $castType) {
            $this->assertArrayHasKey($field, $casts);
            $this->assertEquals($castType, $casts[$field]);
        }
    }

    #[Test]
    public function it_defines_belongs_to_pendaftaran_relation_correctly()
    {
        $evaluasi = new EvaluasiMagangModel();
        $relation = $evaluasi->pendaftaran();

        // Memastikan tipe relasi adalah BelongsTo
        $this->assertInstanceOf(BelongsTo::class, $relation);
        
        // Memastikan Foreign Key yang digunakan
        $this->assertEquals('pendaftaran_id', $relation->getForeignKeyName());

        // Memastikan Model yang terkait adalah Pendaftaran
        $this->assertInstanceOf(Pendaftaran::class, $relation->getRelated());
    }

    #[Test]
    public function it_defines_belongs_to_penilai_relation_correctly()
    {
        $evaluasi = new EvaluasiMagangModel();
        $relation = $evaluasi->penilai();

        // Memastikan tipe relasi adalah BelongsTo
        $this->assertInstanceOf(BelongsTo::class, $relation);

        // Memastikan Foreign Key yang digunakan
        $this->assertEquals('penilai_id', $relation->getForeignKeyName());
        
        // Memastikan Model yang terkait adalah User
        $this->assertInstanceOf(User::class, $relation->getRelated());
    }

    #[Test]
    public function it_defines_belongs_to_template_sertifikat_relation_correctly()
    {
        $evaluasi = new EvaluasiMagangModel();
        $relation = $evaluasi->templateSertifikat();

        // Memastikan tipe relasi adalah BelongsTo
        $this->assertInstanceOf(BelongsTo::class, $relation);

        // Memastikan Foreign Key default digunakan: template_sertifikat_id
        $this->assertEquals('template_sertifikat_id', $relation->getForeignKeyName());
        
        // Memastikan Model yang terkait adalah TemplateSertifikat
        $this->assertInstanceOf(TemplateSertifikatModel::class, $relation->getRelated());
    }
}