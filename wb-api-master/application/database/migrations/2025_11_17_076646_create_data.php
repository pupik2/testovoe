<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');
            $table->date('date');

            $table->string('metric_name');
            $table->bigInteger('metric_value');

            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->unique(['account_id', 'external_id', 'date', 'metric_name']);
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('data');
    }
}
