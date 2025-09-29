<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\faqs;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        faqs::create([
            'question'=>'What is this app used for?',
            'answer'=>'This app allows you to search for doctors, book appointments, and consult in person easily from your phone.',
            'order'=>1,
                        'status'=>true

        ]);
           faqs::create([
            'question'=>'Is the app free to use?',
            'answer'=>'This app allows you to search for doctors, book appointments, and consult in person easily from your phone.',
            'order'=>2,
                        'status'=>true

           ]);
              faqs::create([
            'question'=>'How can I find a doctor?',
            'answer'=>'This app allows you to search for doctors, book appointments, and consult in person easily from your phone.',
            'order'=>3,
            'status'=>true
              ]);
    }
}
