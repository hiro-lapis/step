<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

/**
 * マスタデータ投入seeder
 */
class AchievementTimeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * 【重要】
         * テーブルのtrunateがあるため、本番実行時の瞬断に注意
         * データ追加の際は、配列末尾に追加しsort_numberで順序を設定する
         */

        // テスト実行時にtruncateするとRefreshDatabaseが効かないため、テスト時はtruncateしない
        if (!App::environment('testing')) {
            DB::table('achievement_time_types')->truncate();
        }
        $time_stamp = now();
        // バリデーションのロジックの関係上、idは1から連番にする
        DB::table('achievement_time_types')->insert([
            [
                'id' => 1,
                'name' => '分',
                'display_name' => '分間',
                'sort_number' => 1,
                'created_at' => $time_stamp,
                'updated_at' => $time_stamp,
            ],
            [
                'id' => 2,
                'name' => '時間',
                'display_name' => '時間',
                'sort_number' => 2,
                'created_at' => $time_stamp,
                'updated_at' => $time_stamp,
            ],
            [
                'id' => 3,
                'name' => '日',
                'display_name' => '日間',
                'sort_number' => 3,
                'created_at' => $time_stamp,
                'updated_at' => $time_stamp,
            ],
            [
                'id' => 4,
                'name' => '週',
                'display_name' => '週間',
                'sort_number' => 4,
                'created_at' => $time_stamp,
                'updated_at' => $time_stamp,
            ],
            [
                'id' => 5,
                'name' => '月',
                'display_name' => 'ヶ月間',
                'sort_number' => 5,
                'created_at' => $time_stamp,
                'updated_at' => $time_stamp,
            ],
            [
                'id' => 6,
                'name' => '年',
                'display_name' => '年間',
                'sort_number' => 6,
                'created_at' => $time_stamp,
                'updated_at' => $time_stamp,
            ],
        ]);
    }
}
