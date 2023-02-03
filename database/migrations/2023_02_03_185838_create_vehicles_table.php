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
            $table->integer('vehicle_id');
            $table->integer('node_id')->nullable();
            $table->string('url')->nullable();
            $table->string('plate')->nullable();
            $table->json('coordinate')->nullable();
            $table->string('speed')->nullable();
            $table->string('description')->nullable();
            $table->string('time')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('camera')->nullable();
            $table->boolean('status')->nullable();
            $table->boolean('external')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('vehicle_id');
            $table->index('plate');
            $table->index('url');
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
