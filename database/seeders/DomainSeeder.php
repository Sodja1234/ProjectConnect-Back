<?php

namespace Database\Seeders;

use App\Models\Domain;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $domains = [
        'Informatique',
        'Santé',
        'Éducation',
        'Finance',
        'BTP',
        'Énergie',
        'Agriculture',
        'Commerce',
        'Transport',
    ];

    foreach ($domains as $domain) {
        Domain::create(['name' => $domain]);
    }
    }
}
