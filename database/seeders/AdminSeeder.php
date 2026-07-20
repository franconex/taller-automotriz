<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'rol_id' => 1,
            'nombre' => 'Administrador',
            'username' => 'admin',
            'email' => 'admin@taller.com',
            'password' => Hash::make('12345678'),
            'estado' => true,
        ]);
    }
}
