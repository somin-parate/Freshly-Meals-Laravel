<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreshlyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freshly_users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('emirate_id', 255)->nullable();
            $table->string('fname', 255)->nullable();
            $table->string('lname', 255)->nullable();
            $table->string('email', 50)->unique();
            $table->string('password', 200);
            $table->string('confirm_password', 200);
            $table->string('image', 200)->nullable();
            $table->enum('gender', ['Male', 'Female', 'Both'])->default('Both');
            $table->string('dob', 255)->nullable();
            $table->string('height', 255)->nullable();
            $table->string('weight', 255)->nullable();
            $table->string('blood_group', 255)->nullable();
            $table->string('cardio', 255)->nullable();
            $table->string('weight_training', 255)->nullable();
            $table->string('allergies', 255)->nullable();
            $table->string('mobile_number', 255)->nullable();
            $table->string('med_conditions', 255)->nullable();
            $table->string('remember_token', 255)->nullable();
            $table->string('otp', 255)->nullable();
            $table->string('status', 255)->nullable();
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
        Schema::dropIfExists('freshly_users');
    }
}
