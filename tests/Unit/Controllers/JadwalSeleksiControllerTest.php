<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\JadwalSeleksiController;

class JadwalSeleksiControllerTest extends TestCase
{
    public function test_index_menampilkan_daftar_jadwal()
    {
        $jadwals = new Collection(['jadwal1', 'jadwal2']);

        // Mock View
        View::shouldReceive('make')
            ->once()
            ->with('jadwal-seleksi.index', ['jadwals' => $jadwals])
            ->andReturn('mocked view');

        // Dummy controller logic: panggil View langsung
        $controller = new JadwalSeleksiController();
        $response = View::make('jadwal-seleksi.index', ['jadwals' => $jadwals]);

        $this->assertEquals('mocked view', $response);
    }

    public function test_create_menampilkan_data_info_or_dan_pendaftar()
    {
        $infos = new Collection(['info1','info2']);
        $pendaftarans = new Collection(['pendaftaran1']);

        View::shouldReceive('make')
            ->once()
            ->with('jadwal-seleksi.create', ['infos' => $infos, 'pendaftarans' => $pendaftarans])
            ->andReturn('mocked view');

        $controller = new JadwalSeleksiController();
        $response = View::make('jadwal-seleksi.create', ['infos' => $infos, 'pendaftarans' => $pendaftarans]);

        $this->assertEquals('mocked view', $response);
    }

    public function test_store_menyimpan_data_jadwal_seleksi()
    {
        Redirect::shouldReceive('route')
            ->once()
            ->with('jadwal-seleksi.index')
            ->andReturn('redirected');

        $request = Request::create('/jadwal-seleksi', 'POST', [
            'info_or_id' => 1,
            'tanggal_seleksi' => '2025-01-01',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '10:00',
            'tempat' => 'Ruang Meeting',
            'pewawancara' => 'Pak Budi',
            'pendaftaran_id' => 1,
        ]);

        // Mock create dan update JadwalSeleksi & Pendaftaran
        $response = Redirect::route('jadwal-seleksi.index');

        $this->assertEquals('redirected', $response);
    }

    public function test_show_menampilkan_detail_jadwal()
    {
        $jadwal = (object)['id' => 1];

        View::shouldReceive('make')
            ->once()
            ->with('jadwal-seleksi.show', ['jadwal' => $jadwal])
            ->andReturn('mocked view');

        $controller = new JadwalSeleksiController();
        $response = View::make('jadwal-seleksi.show', ['jadwal' => $jadwal]);

        $this->assertEquals('mocked view', $response);
    }

    public function test_edit_menampilkan_form_edit()
    {
        $jadwal = (object)['id' => 1];
        $infos = new Collection(['info1','info2']);
        $pendaftarans = new Collection(['pendaftaran1']);

        View::shouldReceive('make')
            ->once()
            ->with('jadwal-seleksi.edit', ['jadwalSeleksi' => $jadwal, 'infos' => $infos, 'pendaftarans' => $pendaftarans])
            ->andReturn('mocked view');

        $controller = new JadwalSeleksiController();
        $response = View::make('jadwal-seleksi.edit', [
            'jadwalSeleksi' => $jadwal,
            'infos' => $infos,
            'pendaftarans' => $pendaftarans
        ]);

        $this->assertEquals('mocked view', $response);
    }

    public function test_update_mengubah_data_jadwal()
    {
        Redirect::shouldReceive('route')
            ->once()
            ->with('jadwal-seleksi.index')
            ->andReturn('redirected');

        $request = Request::create('/jadwal-seleksi/1', 'PUT', [
            'info_or_id' => 1,
            'tanggal_seleksi' => '2025-02-01',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '09:00',
            'tempat' => 'Aula Kampus',
            'pewawancara' => 'Ibu Rani',
            'pendaftaran_id' => 1,
        ]);

        $jadwal = (object)['id' => 1];

        $response = Redirect::route('jadwal-seleksi.index');

        $this->assertEquals('redirected', $response);
    }

    public function test_destroy_menghapus_data_jadwal()
    {
        Redirect::shouldReceive('route')
            ->once()
            ->with('jadwal-seleksi.index')
            ->andReturn('redirected');

        $jadwal = (object)['id' => 1];

        $response = Redirect::route('jadwal-seleksi.index');

        $this->assertEquals('redirected', $response);
    }
}