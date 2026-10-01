<?php

namespace Database\Seeders;

use App\Services\SsoProviderService;
use Illuminate\Database\Seeder;

class SsoProviderSeeder extends Seeder
{
    public function run(): void
    {
        app(SsoProviderService::class)->ensureSeeded();
    }
}
