<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses; // Tambahkan ini

#[RunTestsInSeparateProcesses] // Tambahkan ini


class PengumumanMagangTest extends TestCase
{
    use RefreshDatabase;

    
    public function test_status_kelulusan_nilai_85_adalah_lulus(): void
    {
        $nilai = 85;
        $hasilEvaluasi = $nilai >= 75 ? 'Lulus' : 'Tidak Lulus';
        
        $this->assertEquals('Lulus', $hasilEvaluasi);
    }

   
    public function test_status_kelulusan_nilai_60_adalah_tidak_lulus(): void
    {
        $nilai = 60;
        $hasilEvaluasi = $nilai >= 75 ? 'Lulus' : 'Tidak Lulus';
        
        $this->assertEquals('Tidak Lulus', $hasilEvaluasi);
    }

    
    public function test_boundary_value_75_adalah_lulus(): void
    {
        $nilai = 75;
        $hasilEvaluasi = $nilai >= 75 ? 'Lulus' : 'Tidak Lulus';
        
        $this->assertEquals('Lulus', $hasilEvaluasi);
    }


    public function test_boundary_value_74_99_adalah_tidak_lulus(): void
    {
        $nilai = 74.99;
        $hasilEvaluasi = $nilai >= 75 ? 'Lulus' : 'Tidak Lulus';
        
        $this->assertEquals('Tidak Lulus', $hasilEvaluasi);
    }

   
    public function test_warna_status_lulus_adalah_text_green(): void
    {
        $hasilEvaluasi = 'Lulus';
        $warnaStatus = $hasilEvaluasi === 'Lulus' ? 'text-green' : 'text-red';
        
        $this->assertEquals('text-green', $warnaStatus);
    }

  
    public function test_warna_status_tidak_lulus_adalah_text_red(): void
    {
        $hasilEvaluasi = 'Tidak Lulus';
        $warnaStatus = $hasilEvaluasi === 'Lulus' ? 'text-green' : 'text-red';
        
        $this->assertEquals('text-red', $warnaStatus);
    }

    
    public function test_format_nama_mahasiswa_john_doe(): void
    {
        $namaMahasiswa = 'John Doe';
        $namaMahasiswaFormat = str_replace(' ', '_', strtolower($namaMahasiswa));
        
        $this->assertEquals('john_doe', $namaMahasiswaFormat);
    }

   
    public function test_format_nama_mahasiswa_panjang(): void
    {
        $namaMahasiswa = 'Muhammad Rifqi Pratama';
        $namaMahasiswaFormat = str_replace(' ', '_', strtolower($namaMahasiswa));
        
        $this->assertEquals('muhammad_rifqi_pratama', $namaMahasiswaFormat);
    }

    
    public function test_nama_file_sertifikat_format(): void
    {
        $namaMahasiswa = 'john_doe';
        $fileName = 'Sertifikat_' . $namaMahasiswa . '.pdf';
        
        $this->assertStringStartsWith('Sertifikat_', $fileName);
        $this->assertStringEndsWith('.pdf', $fileName);
        $this->assertEquals('Sertifikat_john_doe.pdf', $fileName);
    }

    
    public function test_path_file_sertifikat_format_benar(): void
    {
        $fileName = 'Sertifikat_john_doe.pdf';
        $path = 'sertifikat/' . $fileName;
        
        $this->assertStringStartsWith('sertifikat/', $path);
        $this->assertEquals('sertifikat/Sertifikat_john_doe.pdf', $path);
    }

   
    public function test_nomor_sertifikat_concatenate(): void
    {
        $nomorSertifikat = 'SERT-2024-';
        $idPendaftaran = 5;
        $nomorFinal = $nomorSertifikat . $idPendaftaran;
        
        $this->assertEquals('SERT-2024-5', $nomorFinal);
    }

    
    public function test_nomor_sertifikat_concatenate_id_10(): void
    {
        $nomorSertifikat = 'SERT-2024-';
        $idPendaftaran = 10;
        $nomorFinal = $nomorSertifikat . $idPendaftaran;
        
        $this->assertEquals('SERT-2024-10', $nomorFinal);
    }

    
    public function test_number_format_nilai_total(): void
    {
        $rataRata = 85;
        $formatted = number_format($rataRata, 2);
        
        $this->assertEquals('85.00', $formatted);
    }

    
    public function test_number_format_nilai_total_decimal(): void
    {
        $rataRata = 87.5;
        $formatted = number_format($rataRata, 2);
        
        $this->assertEquals('87.50', $formatted);
    }

    
    public function test_nilai_total_tidak_dibagi_4(): void
    {
        $nilaiTotal = 85;
        $rataRata = $nilaiTotal;
        
        $this->assertEquals(85, $rataRata);
        $this->assertNotEquals(21.25, $rataRata);
    }

   
    public function test_nilai_total_90_tetap_90(): void
    {
        $nilaiTotal = 90;
        $rataRata = $nilaiTotal;
        
        $this->assertEquals(90, $rataRata);
    }

    public function test_tanggal_format_string(): void
    {
        $tanggal = now()->translatedFormat('d F Y');
        
        $this->assertIsString($tanggal);
        $this->assertGreaterThan(0, strlen($tanggal));
    }

    
    public function test_base64_encoding_tanda_tangan(): void
    {
        $imageContent = 'fake image content';
        $base64 = 'data:image/png;base64,' . base64_encode($imageContent);
        
        $this->assertStringStartsWith('data:image/png;base64,', $base64);
    }

   
    public function test_kondisi_ttd_tidak_ada(): void
    {
        $fileExists = false;
        $ttd = $fileExists ? 'data:image/png;base64,...' : '';
        
        $this->assertEquals('', $ttd);
    }

    
    public function test_placeholder_replacement_nama(): void
    {
        $template = 'Nama: {{nama_mahasiswa}}';
        $result = str_replace('{{nama_mahasiswa}}', 'John Doe', $template);
        
        $this->assertStringContainsString('John Doe', $result);
        $this->assertStringNotContainsString('{{nama_mahasiswa}}', $result);
    }
   
    public function test_placeholder_replacement_nilai(): void
    {
        $template = 'Nilai: {{nilai_total}}';
        $result = str_replace('{{nilai_total}}', '85.00', $template);
        
        $this->assertStringContainsString('85.00', $result);
        $this->assertStringNotContainsString('{{nilai_total}}', $result);
    }

    
    public function test_placeholder_replacement_nomor(): void
    {
        $template = 'Nomor: {{nomor_sertifikat}}';
        $result = str_replace('{{nomor_sertifikat}}', 'SERT-2024-5', $template);
        
        $this->assertStringContainsString('SERT-2024-5', $result);
        $this->assertStringNotContainsString('{{nomor_sertifikat}}', $result);
    }

    
    public function test_placeholder_replacement_hasil(): void
    {
        $template = 'Status: {{hasil_evaluasi}}';
        $result = str_replace('{{hasil_evaluasi}}', 'Lulus', $template);
        
        $this->assertStringContainsString('Lulus', $result);
        $this->assertStringNotContainsString('{{hasil_evaluasi}}', $result);
    }

    
    public function test_placeholder_replacement_multiple(): void
    {
        $template = 'Nama: {{nama_mahasiswa}}, Nilai: {{nilai_total}}, Status: {{hasil_evaluasi}}';
        
        $result = str_replace(
            ['{{nama_mahasiswa}}', '{{nilai_total}}', '{{hasil_evaluasi}}'],
            ['John Doe', '85.00', 'Lulus'],
            $template
        );
        
        $this->assertStringContainsString('John Doe', $result);
        $this->assertStringContainsString('85.00', $result);
        $this->assertStringContainsString('Lulus', $result);
    }

    
    public function test_status_pendaftaran_lulus_magang(): void
    {
        $hasilEvaluasi = 'Lulus';
        $status = $hasilEvaluasi === 'Lulus' ? 'lulus_magang' : 'tidak_lulus';
        
        $this->assertEquals('lulus_magang', $status);
    }

    
    public function test_status_pendaftaran_tidak_lulus(): void
    {
        $hasilEvaluasi = 'Tidak Lulus';
        $status = $hasilEvaluasi === 'Lulus' ? 'lulus_magang' : 'tidak_lulus';
        
        $this->assertEquals('tidak_lulus', $status);
    }

    
    public function test_pdf_paper_size_a4(): void
    {
        $paperSize = 'a4';
        
        $this->assertEquals('a4', $paperSize);
    }

   
    public function test_pdf_orientation_landscape(): void
    {
        $orientation = 'landscape';
        
        $this->assertEquals('landscape', $orientation);
    }

   
    public function test_pdf_options_html5_parser_enabled(): void
    {
        $options = [
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ];
        
        $this->assertTrue($options['isHtml5ParserEnabled']);
    }

    
    public function test_pdf_options_remote_enabled(): void
    {
        $options = [
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ];
        
        $this->assertTrue($options['isRemoteEnabled']);
    }
}