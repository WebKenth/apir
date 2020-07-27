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
            $table->string('oid')->index();
            $table->string('plate')->index();
            $table->string('registration_status');
            $table->dateTime('registration_date');
            $table->string('type');
            $table->string('usage');
            $table->string('vin');
            $table->string('model');
            $table->string('brand');
            $table->string('engine');
            $table->string('fuel_type');
            $table->string('inspection_status');
            $table->dateTime('inspection_date');
            $table->jsonb('raw');
            $table->jsonb('raw_dot');
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
