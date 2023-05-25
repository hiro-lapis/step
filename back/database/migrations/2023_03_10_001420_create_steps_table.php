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
        Schema::create('steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('登録ユーザーID');
            $table->unsignedBigInteger('category_id')->nullable()->comment('カテゴリーID');
            $table->string('name')->nullable()->comment('ステップ名');
            $table->text('summary')->nullable()->comment('ステップの概要');
            $table->unsignedBigInteger('achievement_time_type_id')->nullable()->comment('達成目安時間ID');
            $table->unsignedBigInteger('time_count')->nullable()->comment('達成目安時間の時間量');
            $table->boolean('is_active')->default(true)->comment('公開フラグ');
            $table->json('draft')->nullable()->comment('下書き情報');
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('achievement_time_type_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('steps');
    }
};
