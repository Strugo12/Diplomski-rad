<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('destination');
            $table->smallInteger('duration');
            $table->text('date');
            $table->text('image');
            $table->text('description');
            $table->text('guide');
            $table->timestamps();
            $table->text('price');
            $table->text('seats');
            $table->text('freeseats');
            $table->text('remark');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trips');
    }
}