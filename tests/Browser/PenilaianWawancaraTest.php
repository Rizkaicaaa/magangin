<?php

namespace Tests\Browser;

use App\Models\Dinas;
use App\Models\InfoOr;
use App\Models\JadwalSeleksi;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use PHPUnit\Framework\Attributes\Group;

class PenilaianWawancaraTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $pewawancara;
    protected $mahasiswa;
    protected $pendaftaran;
    protected $jadwalSeleksi;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user with 'pewawancara' role
        $this->pewawancara = User::factory()->create(['role' => 'pewawancara']);

        // Create a student user and their registration
        $this->mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $dinas = Dinas::factory()->create();
        $infoOr = InfoOr::factory()->aktif()->create();
        
        $this->pendaftaran = Pendaftaran::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'info_or_id' => $infoOr->id,
            'pilihan_dinas_1' => $dinas->id,
            'pilihan_dinas_2' => $dinas->id,
            'status_pendaftaran' => 'terdaftar',
        ]);

        // Create an interview schedule
        $this->jadwalSeleksi = JadwalSeleksi::factory()->create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'info_or_id' => $infoOr->id,
            'pewawancara' => $this->pewawancara->nama_lengkap,
        ]);
    }

    /**
     * Test interviewer can successfully submit an assessment.
     */
    #[Group('penilaian-wawancara-dusk')]
    public function testInterviewerCanSubmitAssessmentSuccessfully()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->pewawancara)
                    ->visit(route('penilaian-wawancara.create'))
                    ->assertSee('Form Penilaian Wawancara');

            // Select the student from the schedule
            $browser->select('jadwal_seleksi_id', $this->jadwalSeleksi->id)
                    ->type('nilai_komunikasi', '85')
                    ->type('nilai_motivasi', '90')
                    ->type('nilai_kemampuan', '80')
                    ->type('kkm', '75')
                    ->press('Simpan Penilaian')
                    ->assertPathIs(route('penilaian-wawancara.index'))
                    ->assertSee('Penilaian berhasil ditambahkan.');
            
            // Check if the assessment is listed
            $browser->assertSee($this->mahasiswa->nama_lengkap)
                    ->assertSee('85.00'); // Average score of (85+90+80)/3
        });
    }

    /**
     * Test assessment form validation.
     */
    #[Group('penilaian-wawancara-dusk')]
    public function testAssessmentFormValidation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->pewawancara)
                    ->visit(route('penilaian-wawancara.create'))
                    ->press('Simpan Penilaian')
                    ->assertPathIs(route('penilaian-wawancara.create'))
                    ->assertSee('The jadwal seleksi id field is required.')
                    ->assertSee('The nilai komunikasi field is required.')
                    ->assertSee('The nilai motivasi field is required.')
                    ->assertSee('The nilai kemampuan field is required.')
                    ->assertSee('The kkm field is required.');
        });
    }
}
