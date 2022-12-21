<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToUserMealPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_meal_plans', function (Blueprint $table) {
            $table->string('offer_id', 50)->nullable();
            $table->string('coupon_code', 50)->nullable();
            $table->string('discounted_amount', 50)->nullable();
            $table->string('grand_total', 50)->nullable();
            $table->string('offer_status', 50)->nullable();
            $table->string('cart_id', 50)->nullable();
            $table->enum('payment_mode', ['online', 'bank'])->nullable();
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
            $table->dropColumn(['offer_id', 'coupon_code', 'discounted_amount', 'grand_total', 'offer_status', 'cart_id', 'payment_mode']);
        });
    }
}
