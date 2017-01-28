<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beers', function (Blueprint $table) {
            $table->string('api_id')->primary();
            $table->string('brewery_api_id')->nullable();
            $table->string('name', 100);
            $table->text('description', 100)->nullable();
            $table->float('abv')->nullable();
            $table->float('abv_min')->nullable();
            $table->float('abv_max')->nullable();
            $table->string('image_icon')->nullable();
            $table->string('image_medium')->nullable();
            $table->string('image_large')->nullable();
            $table->string('status', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beers');
    }
}