<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToUserAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_address', function (Blueprint $table) {
            $table->string('house_no', 255)->nullable();
            $table->string('area', 255)->nullable();
            $table->string('landmark', 255)->nullable();
            $table->dropColumn(['complete_address', 'how_to_reach']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_address', function (Blueprint $table) {
            $table->dropColumn(['house_no', 'area', 'landmark']);
            $table->string('complete_address', 255)->nullable();
            $table->string('how_to_reach', 255)->nullable();
        });
    }
}
