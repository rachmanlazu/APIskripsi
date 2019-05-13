<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerawatansTable extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perawatans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('jenis_perawatan');
            $table->string('nama_perawatan');
            $table->integer('harga_perawatan');
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
        Schema::dropIfExists('perawatans');
    }
}
