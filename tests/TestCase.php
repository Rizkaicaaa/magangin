<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Disable middleware so unit test tidak memanggil hal-hal
     * yang bisa menyentuh database atau autentikasi.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Nonaktifkan middleware untuk unit test
        $this->withoutMiddleware();
    }
}
