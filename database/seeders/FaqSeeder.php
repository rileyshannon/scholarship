<?php

namespace Database\Seeders;

use App\Enums\FaqType;
use App\Models\FaqItem;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $sort = 1;
        foreach (config('scholarship.frequently_asked_questions') as $question => $answer) {
            FaqItem::create([
                'question'   => $question,
                'answer'     => $answer,
                'type'       => FaqType::Faq,
                'sort_order' => $sort++,
            ]);
        }

        $sort = 1;
        foreach (config('scholarship.eligibility_criteria') as $question => $answer) {
            FaqItem::create([
                'question'   => $question,
                'answer'     => $answer,
                'type'       => FaqType::Eligibility,
                'sort_order' => $sort++,
            ]);
        }
    }
}
