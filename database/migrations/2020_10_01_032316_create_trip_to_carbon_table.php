<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripToCarbonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_to_carbon', function (Blueprint $table) {
            $table->id();
            $table->string('activity');
            $table->text('activityType');
            $table->text('country');
            $table->text('mode');
            $table->text('fuelType');
            $table->text('appTkn');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_to_carbon');
    }
}
