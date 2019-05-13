<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRekamMedisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('status')->nullable();
            $table->timestamps();

            $table->integer('pasien_id')->unsigned();
            $table->integer('perawatan_id')->unsigned();

            $table->foreign('pasien_id')->references('id')->on('pasiens');
            $table->foreign('perawatan_id')->references('id')->on('perawatans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rekam_medis');
    }
}
