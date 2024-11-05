<?php

namespace Database\Seeders;

use App\Models\Conta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Conta::where('nome', 'Energia')->first()) {
            Conta::create([
                'nome' => 'Energia',
                'valor' => '142.47',
                'vencimento' => '2024-10-18',
            ]);
        }

        if (!Conta::where('nome', 'Internet')->first()) {
            Conta::create([
                'nome' => 'Internet',
                'valor' => '119.47',
                'vencimento' => '2024-10-28',
            ]);
        }
        if (!Conta::where('nome', 'Moto')->first()) {
            Conta::create([
                'nome' => 'Moto',
                'valor' => '200.47',
                'vencimento' => '2024-10-08',
            ]);
        }
        if (!Conta::where('nome', 'Gás')->first()) {
            Conta::create([
                'nome' => 'Gás',
                'valor' => '12.47',
                'vencimento' => '2024-10-01',
            ]);
        }
    }
}
