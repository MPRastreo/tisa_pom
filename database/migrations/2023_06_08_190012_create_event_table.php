<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table)
        {
            $table->id();
            $table->string('unit_name');
            $table->boolean('engine_status');
            $table->boolean('panic_button');
            $table->float('main_battery');
            $table->float('ext_battery');
            $table->float('gps_antenna');
            $table->boolean('engine_cutoff')->nullable();
            $table->boolean('jamming')->nullable();
            $table->boolean('status_events');
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
        Schema::dropIfExists('events');
    }
}
