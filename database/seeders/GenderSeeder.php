<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gender;

class GenderSeeder extends Seeder
{
    public function run(): void
    {
        $genders = [
            ['name' => 'Masculino', 'code' => 'M'],
            ['name' => 'Feminino', 'code' => 'F'],
            ['name' => 'Outro', 'code' => 'O'],
            ['name' => 'Não Informado', 'code' => 'N'],
        ];

        foreach ($genders as $gender) {
            Gender::firstOrCreate(['code' => $gender['code']], $gender);
        }
    }
}
