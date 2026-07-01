<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Pemilik Resto', 'email' => 'pemilik@siresto.test', 'role' => User::ROLE_PEMILIK],
            ['name' => 'Pelayan Satu',  'email' => 'pelayan@siresto.test', 'role' => User::ROLE_PELAYAN],
            ['name' => 'Koki Satu',     'email' => 'koki@siresto.test',    'role' => User::ROLE_KOKI],
            ['name' => 'Kasir Satu',    'email' => 'kasir@siresto.test',   'role' => User::ROLE_KASIR],
            ['name' => 'Gudang Satu',   'email' => 'gudang@siresto.test',  'role' => User::ROLE_GUDANG],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name'              => $u['name'],
                    'role'              => $u['role'],
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}