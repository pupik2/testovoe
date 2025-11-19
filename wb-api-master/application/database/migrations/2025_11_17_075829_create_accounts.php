<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('token_value');
            $table->timestamp('expires_at')->nullable();

            $table->foreignId('token_type_id')->constrained('token_types');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('api_service_id')->constrained('api_services');


            $table->foreign(['api_service_id', 'token_type_id'])
                ->references(['api_service_id', 'token_type_id'])
                ->on('service_token_types')
                ->onDelete('restrict');

        $table->timestamps();
    });
}

public
function down()
{
    Schema::dropIfExists('accounts');
}
}
