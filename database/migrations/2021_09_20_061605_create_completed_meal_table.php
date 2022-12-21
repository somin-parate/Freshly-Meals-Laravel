<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompletedMealTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('completed_meal', function (Blueprint $table) {
            $table->id();
            $table->integer('kitchen_userId')->unsigned();
            $table->integer('meal_id')->unsigned();
            $table->integer('quantity')->unsigned();
            $table->enum('packed_status', ['Yes', 'No'])->default('No');
            $table->integer('parcel_userId')->default(0);
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
        Schema::dropIfExists('completed_meal');
    }
}
