<?php

namespace Tests\Browser;

use App\Models\Dinas;
use App\Models\InfoOr;
use App\Models\JadwalSeleksi;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use PHPUnit\Framework\Attributes\Group;

class SeleksiWawancaraTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test a student can see their own upcoming interview schedule.
     */
    #[Group('seleksi-wawancara-dusk')]
    public function testStudentCanSeeTheirSchedule()
    {
        // --- Setup ---
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $otherStudent = User::factory()->create(['role' => 'mahasiswa']);
        $infoOr = InfoOr::factory()->aktif()->create();
        $dinas = Dinas::factory()->create();

        $pendaftaran = Pendaftaran::factory()->create(['user_id' => $student->id, 'info_or_id' => $infoOr->id, 'pilihan_dinas_1' => $dinas->id]);
        $pendaftaranOther = Pendaftaran::factory()->create(['user_id' => $otherStudent->id, 'info_or_id' => $infoOr->id, 'pilihan_dinas_1' => $dinas->id]);

        // Upcoming schedule for the main student
        $upcomingSchedule = JadwalSeleksi::factory()->create([
            'pendaftaran_id' => $pendaftaran->id,
            'info_or_id' => $infoOr->id,
            'tanggal_seleksi' => Carbon::tomorrow()->toDateString(),
            'pewawancara' => 'Bapak Budi',
        ]);

        // Past schedule for the main student (should not be visible)
        $pastSchedule = JadwalSeleksi::factory()->create([
            'pendaftaran_id' => $pendaftaran->id,
            'info_or_id' => $infoOr->id,
            'tanggal_seleksi' => Carbon::yesterday()->toDateString(),
            'pewawancara' => 'Ibu Ani (Lama)',
        ]);

        // Schedule for another student (should not be visible)
        $otherStudentSchedule = JadwalSeleksi::factory()->create([
            'pendaftaran_id' => $pendaftaranOther->id,
            'info_or_id' => $infoOr->id,
            'tanggal_seleksi' => Carbon::tomorrow()->toDateString(),
            'pewawancara' => 'Pewawancara Rahasia',
        ]);

        // --- Test ---
        $this->browse(function (Browser $browser) use ($student, $upcomingSchedule, $pastSchedule, $otherStudentSchedule) {
            $browser->loginAs($student)
                    ->visit(route('mahasiswa.jadwal-seleksi'))
                    ->assertSee('Jadwal Seleksi Wawancara Anda')
                    // Assert upcoming schedule is visible
                    ->assertSee($upcomingSchedule->pewawancara)
                    ->assertSee(Carbon::parse($upcomingSchedule->tanggal_seleksi)->format('d F Y'))
                    // Assert past schedule is NOT visible
                    ->assertDontSee($pastSchedule->pewawancara)
                    // Assert other student's schedule is NOT visible
                    ->assertDontSee($otherStudentSchedule->pewawancara);
        });
    }

    /**
     * Test a student sees a message when no schedule is available.
     */
    #[Group('seleksi-wawancara-dusk')]
    public function testStudentSeesMessageWithNoSchedule()
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        
        $this->browse(function (Browser $browser) use ($student) {
            $browser->loginAs($student)
                    ->visit(route('mahasiswa.jadwal-seleksi'))
                    ->assertSee('Jadwal Seleksi Wawancara Anda')
                    ->assertSee('Saat ini belum ada jadwal seleksi untuk Anda.');
        });
    }
}
