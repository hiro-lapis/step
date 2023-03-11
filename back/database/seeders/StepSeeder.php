<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 本番で実行できないよう設定
        if (config('app.env') === 'production') return

        DB::table('steps')->truncate();
        try {
            DB::beginTransaction();
            DB::table('steps')->insert([
                [
                    'name' => 'テスト',
                    'user_id' => 1,
                    'category_id' => 1,
                    'achievement_time_type_id' => 1, // 1日以内
                ]
            ]);

            DB::commit();
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            DB::rollBack();
        }

    }
}
