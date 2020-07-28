<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('oid')->index()->nullable();
            $table->string('plate')->index()->nullable();
            $table->string('registration_status')->nullable();
            $table->dateTime('registration_date');
            $table->string('type')->nullable();
            $table->string('usage')->nullable();
            $table->string('vin')->nullable();
            $table->string('model')->nullable();
            $table->string('brand')->nullable();
            $table->string('engine')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('inspection_status')->nullable();
            $table->dateTime('inspection_date');
            $table->jsonb('raw')->nullable();
            $table->jsonb('raw_dot')->nullable();
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
        Schema::dropIfExists('vehicles');
    }
}
