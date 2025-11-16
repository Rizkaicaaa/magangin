<?php

namespace Tests\Unit\Models;

use App\Models\HasilSeleksi;
use Mockery;
use Tests\TestCase;

class HasilSeleksiTest extends TestCase
{
    /** @test */
    public function uji_create_hasil_seleksi()
    {
        // Arrange
        $data = [
            'ID_Nilai_Wawancara' => 1,
            'Nilai_Total' => 85,
            'Status_Seleksi' => 'Lulus',
        ];

        // Mock model
        $mock = Mockery::mock(HasilSeleksi::class)->makePartial();
        $mock->shouldReceive('create')
             ->once()
             ->with($data)
             ->andReturn(new HasilSeleksi($data));

        // Act
        $hasilSeleksi = $mock->create($data);

        // Assert
        $this->assertInstanceOf(HasilSeleksi::class, $hasilSeleksi);
        $this->assertEquals($data['ID_Nilai_Wawancara'], $hasilSeleksi->ID_Nilai_Wawancara);
        $this->assertEquals($data['Nilai_Total'], $hasilSeleksi->Nilai_Total);
        $this->assertEquals($data['Status_Seleksi'], $hasilSeleksi->Status_Seleksi);
    }

    /** @test */
    public function uji_fillable_fields_hasil_seleksi()
    {
        // Arrange
        $model = new HasilSeleksi();

        // Act
        $fillable = $model->getFillable();

        // Assert
        $this->assertEquals([
            'ID_Nilai_Wawancara',
            'Nilai_Total',
            'Status_Seleksi',
        ], $fillable);
    }
}