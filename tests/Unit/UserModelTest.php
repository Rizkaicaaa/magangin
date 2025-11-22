<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    /** @test */
    public function user_memiliki_fillable_yang_sesuai()
    {
        $user = new User;

        $this->assertEquals([
            'email',
            'password',
            'role',
            'nama_lengkap',
            'nim',
            'no_telp',
            'tanggal_daftar',
            'status',
            'dinas_id',
        ], $user->getFillable());
    }

    /** @test */
    public function user_memiliki_hidden_yang_sesuai()
    {
        $user = new User;

        $this->assertEquals([
            'password',
            'remember_token',
        ], $user->getHidden());
    }

    /** @test */
    public function user_memiliki_casts_yang_sesuai()
    {
        $user = new User;

         $casts = $user->getCasts();

    $this->assertEquals('datetime', $casts['email_verified_at']);
    $this->assertEquals('date', $casts['tanggal_daftar']);
    $this->assertEquals('hashed', $casts['password']);
    }

    /** @test */
    public function user_mendefinisikan_relasi_dinas()
    {
        $user = new User;

        $relation = $user->dinas();

        $this->assertInstanceOf(BelongsTo::class, $relation);
    }

    /** @test */
    public function user_mendefinisikan_relasi_pendaftaran()
    {
        $user = new User;

        $relation = $user->pendaftaran();

        $this->assertInstanceOf(HasMany::class, $relation);
    }

    /** @test */
    public function user_mendefinisikan_relasi_penilaian_wawancara()
    {
        $user = new User;

        $relation = $user->penilaianWawancaraAsPenilai();

        $this->assertInstanceOf(HasMany::class, $relation);
    }

    /** @test */
    public function user_mendefinisikan_relasi_evaluasi_magang()
    {
        $user = new User;

        $relation = $user->evaluasiMagangAsPenilai();

        $this->assertInstanceOf(HasMany::class, $relation);
    }
}