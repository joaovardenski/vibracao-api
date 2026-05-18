<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name' => 'Administrador',
            'email' => 'admin@vibracaojovem.com',
            'password' => 'admin123',
        ]);
    }
}