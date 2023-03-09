<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * 【重要】運用開始以降は、運用開始時点のカテゴリーデータを変更しない(データ整合性のため)
         *  sort_numberのみ、変えてもOK
         *
         *  新カテゴリーを作成する場合は、末尾に追加していくこと
         */
        // id値をリセットするためテーブルを破棄
        DB::table('categories')->truncate();
        $time_stamp = now();
        DB::table('categories')->insert([
            [
                'name' => 'プログラミング',
                'sort_number' => 1,
                'uri' => 'programming',
                'created_at' => $time_stamp,
                'updated_at' => $time_stamp,
            ],
            [
                'name' => '英語',
                'uri' => 'english',
                'sort_number' => 2,
                'created_at' => $time_stamp,
                'updated_at' => $time_stamp,
            ],
            [
                'name' => 'ダイエット',
                'uri' => 'diet',
                'sort_number' => 3,
                'created_at' => $time_stamp,
                'updated_at' => $time_stamp,
            ],
            [
                'name' => '朝活',
                'uri' => 'morning-activity',
                'sort_number' => 4,
                'created_at' => $time_stamp,
                'updated_at' => $time_stamp,
            ],
        ]);
    }
}
