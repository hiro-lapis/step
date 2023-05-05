<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_gpt_usage_information', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('利用ユーザー');
            $table->date('date')->comment('利用日');
            // laravelのcountとの競合回避のため、usage_countとする
            $table->unsignedSmallInteger('usage_count')->default(0)->comment('利用回数(1日単位)');

            $table->timestamps();

            $table->index('user_id');
            $table->index('date');
            // 同じ日の同じユーザーの利用情報は1つ
            $table->unique(['user_id', 'date']);
            $table->comment('1日単位のユーザー毎chatGPTAPI利用記録');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_gpt_usage_information');
    }
};
