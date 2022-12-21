<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInUserMealPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_meal_plans', function (Blueprint $table) {
            $table->string('total_meal', 50)->nullable();
            $table->string('remaining_meal', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_meal_plans', function (Blueprint $table) {
            $table->dropColumn(['total_meal','remaining_meal']);
        });
    }
}
