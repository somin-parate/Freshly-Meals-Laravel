<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKithenUserMealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kithen_user_meals', function (Blueprint $table) {
            $table->id();
            $table->integer('meal_id')->unsigned();
            $table->integer('food_item_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('plan_shortcode', 50)->nullable();
            $table->integer('quantity')->unsigned();
            $table->enum('status', ['Ready', 'Pending'])->default('Pending');
            $table->date('preparation_at');
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
        Schema::dropIfExists('kithen_user_meals');
    }
}
