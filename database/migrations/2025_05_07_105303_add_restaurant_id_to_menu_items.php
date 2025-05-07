<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('menu_items', function (Blueprint $table) {
        // Add the restaurant_id column if it doesn't already exist
        if (!Schema::hasColumn('menu_items', 'restaurant_id')) {
            $table->unsignedBigInteger('restaurant_id');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
        }
    });
}

public function down()
{
    Schema::table('menu_items', function (Blueprint $table) {
        $table->dropForeign(['restaurant_id']);
        $table->dropColumn('restaurant_id');
    });
}

};
