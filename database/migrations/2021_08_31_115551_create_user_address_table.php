<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_address', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 255)->nullable();
            $table->string('current_location', 255)->nullable();
            $table->string('complete_address', 255)->nullable();
            $table->string('how_to_reach', 255)->nullable();
            $table->string('location_tag', 255)->nullable();
            $table->string('select_location', 100)->nullable();
            $table->string('timeslot_by_emirate', 100)->nullable();
            $table->string('lat', 100)->nullable();
            $table->string('long', 100)->nullable();
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
        Schema::dropIfExists('user_address');
    }
}
