<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountIdToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            Schema::table('sales', function (Blueprint $table) {
                $table->foreignId('account_id')->after('id')->nullable()->constrained('accounts')->onDelete('cascade');
                $table->unique(['account_id', 'sale_id']);
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropUnique(['account_id', 'sale_id']);
                $table->dropForeign(['account_id']);
                $table->dropColumn('account_id');
            });
        });
    }
}
