<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ReceptionistUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'reception@idg.com'],
            [
                'name' => 'Reception Test',
                'email' => 'reception@idg.com',
                'password' => Hash::make('password'),
                'role' => 'receptionist',
            ]
        );
    }
}
