<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEndDateToUserMealPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_meal_plans', function (Blueprint $table) {
            $table->string('end_date', 50)->nullable();
            $table->string('pause_start_date', 50)->nullable();
            $table->string('pause_end_date', 50)->nullable();
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
            $table->dropColumn(['end_date','pause_start_date','pause_end_date']);
        });
    }
}
