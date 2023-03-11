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
            $table->unsignedBigInteger('category_id')->comment('カテゴリーID');
            $table->string('name')->comment('ステップ名');
            $table->time('achievement_time_type_id')->comment('達成目安時間ID');
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
