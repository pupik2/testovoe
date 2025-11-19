<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountToIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->foreignId('account_id')->after('id')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->unique(['account_id', 'income_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropUnique(['account_id', 'income_id']);
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });
    }
}
