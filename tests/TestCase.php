<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Jalankan seeder setelah migrasi agar data user, pegawai,
     * jabatan, shift, dll. tersedia di database testing (SQLite in-memory).
     */
    protected bool $seed = true;
}
