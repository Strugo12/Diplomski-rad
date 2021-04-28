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
            $table->foreignId('guide')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->smallInteger('price');
            $table->smallInteger('seats');
            $table->smallInteger('freeseats');
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