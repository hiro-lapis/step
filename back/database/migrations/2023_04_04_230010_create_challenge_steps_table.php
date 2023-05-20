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
        Schema::create('challenge_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_user_id')->comment('チャレンジしているユーザーID');
            $table->timestamp('challenged_at')->nullable()->comment('チャレンジ開始日時');
            $table->datetime('cleared_at')->nullable()->comment('チャレンジ終了日時');
            $table->unsignedTinyInteger('status')->default(0)->comment('チャレンジ状況(初期値0:挑戦中)');

            // ここからチャレンジ開始時のステップ情報カラム
            $table->unsignedBigInteger('step_id')->comment('元となるステップID');
            $table->unsignedBigInteger('post_user_id')->comment('チャレンジを投稿したユーザーID');
            $table->unsignedBigInteger('category_id')->comment('カテゴリーID');
            $table->unsignedBigInteger('achievement_time_type_id')->comment('達成目安期間ID');
            $table->unsignedBigInteger('time_count')->comment('達成目安時間の時間量');
            $table->string('name')->comment('ステップ名');
            $table->text('summary')->nullable()->comment('ステップの概要');
            $table->timestamps();
            $table->softDeletes();

            $table->index('step_id');
            $table->index('challenge_user_id');
            $table->index('category_id');
            $table->index('achievement_time_type_id');
            $table->comment('チャレンジ状況と開始時のステップ情報');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('challenge_steps');
    }
};
