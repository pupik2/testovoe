<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomes extends Migration
{

    public function up()
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->Integer('income_id');
            $table->string('number')->nullable();
            $table->timestamp('date');
            $table->timestamp('last_change_date');
            $table->string('supplier_article');
            $table->string('tech_size');
            $table->string('barcode');
            $table->Integer('quantity');
            $table->decimal('total_price');
            $table->timestamp('date_close');
            $table->string('warehouse_name');
            $table->Integer('nm_id');
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
        Schema::dropIfExists('_incomes');
    }
}
