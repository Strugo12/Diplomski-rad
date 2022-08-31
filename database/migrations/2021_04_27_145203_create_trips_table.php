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
            $table->smallInteger('duration_days');
            $table->date('date');
            $table->time('time');
            $table->text('image_url');
            $table->text('description');
            $table->foreignId('guide')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->smallInteger('price');
            $table->smallInteger('seats');
            $table->smallInteger('free_seats');
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
