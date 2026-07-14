<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'app_name'=>'Admin Laravel',
            'copyright'=>' Admin Laravel || 2026',
            'login_title'=>'Admin Login',
            'description'=>'Template starter kit admin panel Laravel siap pakai dengan fitur manajemen user, role-permission, dan dashboard responsif untuk mempercepat pengembangan aplikasi web Anda',
            'keywords'=>'laravel admin panel, laravel starter kit, laravel admin boilerplate, root admin laravel, laravel dashboard template, open source laravel admin, laravel filament alternative',
        ]);
    }
}
