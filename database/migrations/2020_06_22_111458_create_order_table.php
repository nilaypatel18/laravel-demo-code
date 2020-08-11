<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('lead_id');
            $table->string('opharma_reference');
            $table->string('prescription_file');
            $table->string('prescription_file_url');
            $table->string('order_date');
            $table->tinyInteger('delivery_type')->comment('Normal 1,Express 2')->nullable();
            $table->decimal('delivery_charge')->nullable();
            $table->decimal('basket_value')->nullable();
            $table->decimal('total_cost')->nullable();
            $table->string('last_subscription_order')->nullable();
            $table->integer('address_id')->nullable();
            $table->tinyInteger('order_status')->comment('Submitted 1,Processing 2,Dispatched 3,Delivered 4,Cancelled 5,Attempted 6,Unreachable 7')->nullable();
            $table->tinyInteger('is_active')->default('1');
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
        Schema::dropIfExists('order');
    }
}
