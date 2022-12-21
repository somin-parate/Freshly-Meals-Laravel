<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->string('user_id',255)->nullable();
            $table->string('cart_id',255)->nullable();
            $table->string('transaction_reference',255)->nullable();
            $table->string('is_pending',255)->nullable();
            $table->string('is_on_hold',255)->nullable();
            $table->string('cart_amount',255)->nullable();
            $table->string('card_scheme',255)->nullable();
            $table->string('card_type',255)->nullable();
            $table->string('payment_description',255)->nullable();
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
        Schema::dropIfExists('payment_details');
    }
}
