<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(AdminSeeder::class);

        \App\Models\User::factory(20)->create();
        \App\Models\Carro::factory(10)->create();
        \App\Models\Aluguel::factory(30)->create(); // Gerando aluguéis com relacionamento
    }
}
