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
        Schema::create('challenge_sub_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_step_id')->comment('親となるチャレンジステップのID');
            $table->timestamp('challenged_at')->nullable()->comment('チャレンジ開始日時');
            $table->datetime('cleared_at')->nullable()->comment('チャレンジ終了日時');
            $table->unsignedTinyInteger('status')->default(1)->comment('チャレンジ状況(初期値0:挑戦中)');

            // ここから元となるサブステップの情報
            $table->unsignedBigInteger('sub_step_id')->comment('元となるサブステップのID');
            $table->string('name')->comment('サブステップ名');
            $table->string('detail')->comment('詳細');
            $table->unsignedTinyInteger('sort_number')->comment('並び順');
            $table->timestamps();
            $table->softDeletes();

            $table->index('challenge_step_id');
            $table->index('sub_step_id');
            $table->comment('チャレンジ開始時のステップに紐づくサブステップ情報');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('challenge_sub_steps');
    }
};
