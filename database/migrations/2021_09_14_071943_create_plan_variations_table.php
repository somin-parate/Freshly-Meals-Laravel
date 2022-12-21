<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_variations', function (Blueprint $table) {
            $table->id();
            $table->integer('plan_id')->unsigned();
            $table->integer('weeks')->unsigned();
            $table->integer('days')->unsigned();
            $table->integer('meals')->unsigned();
            $table->integer('snacks')->unsigned();
            $table->string('variation_id', 100)->nullable();
            $table->float('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_variations');
    }
}
