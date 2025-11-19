<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountIdToStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            Schema::table('stocks', function (Blueprint $table) {
                Schema::table('stocks', function (Blueprint $table) {
                    $table->foreignId('account_id')->after('id')->nullable()->constrained('accounts')->onDelete('cascade');
                    $table->unique(['account_id', 'date', 'supplier_article', 'warehouse_name']);
                });
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
        Schema::table('stocks', function (Blueprint $table) {
            Schema::table('stocks', function (Blueprint $table) {
                Schema::table('stocks', function (Blueprint $table) {
                    $table->dropUnique(['account_id', 'stock_id']);
                    $table->dropForeign(['account_id']);
                    $table->dropColumn('account_id');
                });
            });
        });
    }
}
