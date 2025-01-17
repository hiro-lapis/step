<?php

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
        Schema::table('challenge_steps', function (Blueprint $table) {
            $table->text('image_url')->nullable()->after('name')->comment('画像URL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('challenge_steps', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
};
