<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropCategoryIdFromItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
        if (Schema::hasColumn('items', 'category_id')) {
            $table->dropColumn('category_id');
        }
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
        if (!Schema::hasColumn('items', 'category_id')) {
            $table->unsignedBigInteger('category_id')->after('condition_id');
        }
        });
    }
}
