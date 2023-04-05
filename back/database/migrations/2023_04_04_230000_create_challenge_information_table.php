<?php declare(strict_types=1);

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
        Schema::create('challenge_information', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('チャレンジするユーザーのID');
            $table->unsignedInteger('challenge_count')->default(0)->comment('チャレンジした回数');
            $table->unsignedInteger('clear_count')->default(0)->comment('チャレンジクリア回数');
            $table->unsignedInteger('fail_count')->default(0)->comment('チャレンジ失敗回数');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->comment('ユーザー毎のチャレンジ関連情報');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('challenge_information');
    }
};
