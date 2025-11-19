<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->date('last_change_date')->nullable();
            $table->string('supplier_article')->nullable();
            $table->string('tech_size')->nullable();
            $table->string('barcode')->nullable();
            $table->Integer('quantity')->nullable();
            $table->boolean('is_supply')->default(false)->nullable();
            $table->boolean('is_realization')->default(false)->nullable();
            $table->Integer('quantity_full')->nullable();
            $table->string('warehouse_name')->nullable();
            $table->Integer('in_way_to_client')->nullable();
            $table->Integer('in_way_from_client')->nullable();
            $table->Integer('nm_id')->nullable();
            $table->string('subject')->nullable();
            $table->string('category')->nullable();
            $table->string('brand')->nullable();
            $table->Integer('sc_code')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->Integer('discount')->nullable();
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
        Schema::dropIfExists('_stocks');
    }
}
