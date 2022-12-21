<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToFreshlyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('freshly_users', function (Blueprint $table) {
            $table->string('source_type', 50)->nullable();
            $table->string('app_id', 50)->nullable();
            $table->string('user_name', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('freshly_users', function (Blueprint $table) {
            $table->dropColumn(['source_type','app_id','user_name']);
        });
    }
}
