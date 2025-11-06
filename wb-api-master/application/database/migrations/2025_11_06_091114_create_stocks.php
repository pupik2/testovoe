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
            $table->date('date');
            $table->date('last_change_date');
            $table->string('supplier_article');
            $table->string('tech_size');
            $table->string('barcode');
            $table->Integer('quantity');
            $table->boolean('is_supply')->default(false);
            $table->boolean('is_realization')->default(false);
            $table->Integer('quantity_full');
            $table->string('warehouse_name');
            $table->Integer('in_way_to_client');
            $table->Integer('in_way_from_client');
            $table->Integer('nm_id');
            $table->string('subject');
            $table->string('category');
            $table->string('brand');
            $table->Integer('sc_code');
            $table->decimal('price', 10, 2);
            $table->Integer('discount');
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
