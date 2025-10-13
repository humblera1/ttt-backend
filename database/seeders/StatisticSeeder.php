<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Statistic;
use App\Models\User;
use Illuminate\Database\Seeder;

class StatisticSeeder extends Seeder
{
    private const int USERS_POOL_LIMIT = 50;
    private const int QUESTIONS_POOL_LIMIT = 50;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::inRandomOrder()->limit(self::USERS_POOL_LIMIT)->get();
        $questions = Question::inRandomOrder()->limit(self::QUESTIONS_POOL_LIMIT)->get();

        if ($users->isEmpty() || $questions->isEmpty()) {
            $this->command->warn('StatisticSeeder: there is no users/questions in the system');

            return;
        }

        Statistic::factory()
            ->count(30)
            ->make()
            ->each(function (Statistic $statistic) use ($users, $questions) {
                $statistic->user()->associate($users->random());

                $statistic->question()->associate($questions->random());

                $statistic->save();
            });
    }
}
