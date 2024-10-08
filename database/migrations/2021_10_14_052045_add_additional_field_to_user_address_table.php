<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldToUserAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_address', function (Blueprint $table) {
            $table->string('time_slot_id', 50)->nullable();
            $table->string('city_id', 50)->nullable();
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
            $table->dropColumn(['time_slot_id','city_id']);
        });
    }
}
