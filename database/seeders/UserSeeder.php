<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        User::create([
        'role' => 'admin',
        'username'=> 'nino',
        'email' => 'nino@gmail.com',
        'password' => '123',
        'name' => 'Ario Elnino',
        'phone' => '0876543212',
        'gender' => 'Laki laki',
        'picture' => null,
        ]);

        User::create([
        'role' => 'pembeli',
        'username'=> 'ario',
        'email' => 'ario@gmail.com',
        'password' => '123',
        'name' => 'Ariel',
        'phone' => '08765432222',
        'gender' => 'Laki laki',
        'picture' => null,
        ]);
    }
}
