<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('transaction_settings')->insert([
            'min_amount' => 50,
            'max_amount' => 10000,
            'currency'   => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
