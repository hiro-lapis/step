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
        Schema::create('sub_steps', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('サブステップ名');
            $table->string('detail')->comment('詳細');
            $table->unsignedBigInteger('step_id')->comment('メインのステップID');
            $table->unsignedTinyInteger('sort_number')->comment('並び順');

            $table->index('step_id');
            $table->unique(['id', 'sort_number']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_steps');
    }
};
