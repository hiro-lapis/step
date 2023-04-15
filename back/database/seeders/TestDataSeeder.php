<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Step;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::beginTransaction();
            // ユーザー作成
            $jsonFile = file_get_contents(database_path('seeders/data/user.json'));
            $user_datas = collect(json_decode($jsonFile));
            $users = collect([]);
            // each()はコレクションの各要素に対し、User::create()を実行
            $user_datas->each(function ($user) use (&$users) {
                $users->push(User::firstOrCreate(
                [
                    'email' => $user->email,
                ],
                [
                    'name' => $user->name,
                    'image_url' => $user->image_url,
                    'profile' => $user->profile,
                    'profile' => $user->profile,
                    'password' => Hash::make('password'),
                ]));
            });

            $step_json = file_get_contents(database_path('seeders/data/step.json'));
            $step_datas = collect(json_decode($step_json));
            // each()はコレクションの各要素に対しつつ、Step::create()を実行
            $steps = collect([]);
            $step_datas->each(function ($step_data) use (&$steps, $users) {
                $step = Step::firstOrCreate(
                    [
                        'name' => $step_data->name,
                    ],
                    [
                        'summary' => $step_data->summary,
                        'user_id' => $users->random()->id,
                        'category_id' => $step_data->category_id,
                        'achievement_time_type_id' => $step_data->achievement_time_type_id,
                    ]);
                    if ($step->subSteps()->count() === 0) {
                        foreach ($step_data->sub_steps as $key => $value) {
                            $step->subSteps()->create((array)$value);
                        }
                    }
                    $steps->push($step);
            });
            \Log::info('HIRO:resultの中身' . print_r($steps, true));
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
        }
    }
}
