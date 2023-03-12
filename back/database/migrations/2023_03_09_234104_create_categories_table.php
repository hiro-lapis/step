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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('カテゴリー');
            $table->unsignedSmallInteger('sort_number')->comment('並び順');
            $table->string('uri')->comment('カテゴリーURI');
            $table->timestamps();
            $table->softDeletes();

            $table->unique('uri');
            $table->unique('sort_number');

            // デフォで設定されていたため不要
            // $table->charset = 'utf8mb4';
            // $table->collation = 'utf8mb4_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
