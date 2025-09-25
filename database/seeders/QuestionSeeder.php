<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gradeIds = Grade::pluck('id')->all();

        Question::factory()
            ->count(5)
            ->hasTags(2)
            ->hasCompanies()
            ->create()
            ->each(function ($question) use ($gradeIds) {
                $randomAmount = rand(1, count($gradeIds));

                $question->grades()->attach(
                    collect($gradeIds)->random($randomAmount)->all()
                );
            });
    }
}
