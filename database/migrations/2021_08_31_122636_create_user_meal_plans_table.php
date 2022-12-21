<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMealPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_meal_plans', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 255)->nullable();
            $table->string('meal_plan_id', 255)->nullable();
            $table->string('variation_id', 255)->nullable();
            $table->string('book_nutritionist', 100)->nullable();
            $table->string('start_date', 100)->nullable();
            $table->string('status', 100)->nullable();
            $table->string('order_number', 100)->nullable();
            $table->longText('order_address')->nullable();
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
        Schema::dropIfExists('user_meal_plans');
    }
}
