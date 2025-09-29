<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\pages;
class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        pages::create([
    'type' => 'privacy-policy',
    'title' => 'Privacy Policy',
    'content' => 'Bllllll...'
]);

pages::create([
    'type' => 'terms-and-conditions',
    'title' => 'Terms & Conditions',
    'content' => 'BLLLL...'
]);
    }
}
