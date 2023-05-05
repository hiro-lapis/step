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
        Schema::create('chat_gpt_prompts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('ユーザーID');
            $table->unsignedBigInteger('api_type')->comment('API種別');
            $table->boolean('success')->comment('APIの成功可否');
            $table->text('prompt')->comment('プロンプト');
            $table->text('response')->nullable()->comment('チャットGPTの応答');
            $table->timestamps();

            $table->index('user_id');
            $table->index(['user_id', 'api_type']);
            $table->comment('chat GPTのプロンプトと応答管理テーブル');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_gpt_prompts');
    }
};
