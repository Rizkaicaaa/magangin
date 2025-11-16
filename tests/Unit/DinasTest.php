<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Dinas;
use App\Models\User;
use App\Models\Pendaftaran;
use App\Models\InfoOr;

class DinasTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_dinas_instance()
    {
        $dinas = Dinas::factory()->create([
            'nama_dinas' => 'Dinas Kominfo',
            'deskripsi' => 'Mengelola informasi publik dan teknologi.',
            'kontak_person' => '081234567890',
        ]);

        $this->assertDatabaseHas('dinas', [
            'nama_dinas' => 'Dinas Kominfo',
        ]);
        $this->assertInstanceOf(Dinas::class, $dinas);
    }

    /** @test */
    public function it_has_expected_fillable_attributes()
    {
        $dinas = new Dinas();

        $this->assertEquals([
            'nama_dinas',
            'deskripsi',
            'kontak_person',
        ], $dinas->getFillable());
    }

    /** @test */
    public function it_returns_users_relation()
    {
        $dinas = Dinas::factory()->create();
        $user = User::factory()->create(['dinas_id' => $dinas->id]);

        $this->assertTrue($dinas->users->contains($user));
    }

    /** @test */
    public function it_returns_pendaftaran_pilihan1_relation()
    {
        $dinas = Dinas::factory()->create();
        $infoOr = InfoOr::factory()->create();
        $pendaftaran = Pendaftaran::factory()->create([
            'pilihan_dinas_1' => $dinas->id,
            'info_or_id' => $infoOr->id,
        ]);

        $this->assertTrue($dinas->pendaftaranPilihan1->contains($pendaftaran));
    }

    /** @test */
    public function it_returns_pendaftaran_pilihan2_relation()
    {
        $dinas = Dinas::factory()->create();
        $infoOr = InfoOr::factory()->create();
        $pendaftaran = Pendaftaran::factory()->create([
            'pilihan_dinas_2' => $dinas->id,
            'info_or_id' => $infoOr->id,
        ]);

        $this->assertTrue($dinas->pendaftaranPilihan2->contains($pendaftaran));
    }

    /** @test */
    public function it_returns_pendaftaran_diterima_relation()
    {
        $dinas = Dinas::factory()->create();
        $infoOr = InfoOr::factory()->create();
        $pendaftaran = Pendaftaran::factory()->create([
            'dinas_diterima_id' => $dinas->id,
            'info_or_id' => $infoOr->id,
        ]);

        $this->assertTrue($dinas->pendaftaranDiterima->contains($pendaftaran));
    }

    /** @test */
    public function it_calculates_total_pendaftar_pilihan1_correctly()
    {
        $dinas = Dinas::factory()->create();
        $infoOr = InfoOr::factory()->create();

        Pendaftaran::factory(2)->create([
            'pilihan_dinas_1' => $dinas->id,
            'info_or_id' => $infoOr->id,
        ]);

        $this->assertEquals(2, $dinas->total_pendaftar_pilihan1);
    }

    /** @test */
    public function it_calculates_total_pendaftar_pilihan2_correctly()
    {
        $dinas = Dinas::factory()->create();
        $infoOr = InfoOr::factory()->create();

        Pendaftaran::factory(3)->create([
            'pilihan_dinas_2' => $dinas->id,
            'info_or_id' => $infoOr->id,
        ]);

        $this->assertEquals(3, $dinas->total_pendaftar_pilihan2);
    }

    /** @test */
    public function it_calculates_total_pendaftar_correctly()
    {
        $dinas = Dinas::factory()->create();
        $infoOr = InfoOr::factory()->create();

        Pendaftaran::factory(2)->create([
            'pilihan_dinas_1' => $dinas->id,
            'info_or_id' => $infoOr->id,
        ]);

        Pendaftaran::factory(3)->create([
            'pilihan_dinas_2' => $dinas->id,
            'info_or_id' => $infoOr->id,
        ]);

        $this->assertEquals(5, $dinas->total_pendaftar);
    }

    /** @test */
    public function it_calculates_total_diterima_correctly()
    {
        $dinas = Dinas::factory()->create();
        $infoOr = InfoOr::factory()->create();

        Pendaftaran::factory(4)->create([
            'dinas_diterima_id' => $dinas->id,
            'info_or_id' => $infoOr->id,
        ]);

        $this->assertEquals(4, $dinas->total_diterima);
    }
}