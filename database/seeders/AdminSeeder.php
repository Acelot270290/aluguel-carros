<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; 

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'contato@alandiniz.com.br',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'zip_code' => '25710-372',
            'street' => 'Estrada do Samambaia', 
            'number' => '335',
            'city' => 'Petropolis', 
            'neighborhood' => 'Samambaia', 
            'state' => 'RJ',
            'img' => 'path/to/image.jpg',
        ]);
    }
}
