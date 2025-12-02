<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;
use Random\RandomException;

class QuestionSeeder extends Seeder
{
    /**
     * Вероятность (в процентах) того, что у вопроса будет автор.
     */
    protected const int AUTHOR_PROBABILITY = 60;

    /**
     * Run the database seeds.
     * @throws RandomException
     */
    public function run(): void
    {
        $gradeIds = Grade::pluck('id')->all();
        $userIds = User::inRandomOrder()->limit(15)->pluck('id')->all();


        Question::factory()
            ->count(5)
            ->hasTags(2)
            ->hasCompanies()
            ->hasPositions()
            ->create()
            ->each(function ($question) use ($gradeIds, $userIds) {
                $randomAmount = rand(1, count($gradeIds));

                $question->grades()->attach(
                    collect($gradeIds)->random($randomAmount)->all()
                );

                if ($userIds && random_int(1, 100) <= self::AUTHOR_PROBABILITY) {
                    $randomUserId = collect($userIds)->random();

                    $question->user()->associate($randomUserId);

                    $question->save();
                }
            });
    }
}
