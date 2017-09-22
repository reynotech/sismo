<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePuntosMapasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('puntos_mapas', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
			$table->string('ubicacion');
			$table->float('lat');
			$table->float('lng');
			$table->string('email');
			$table->string('telefono');
			$table->integer('afectados');
			$table->integer('muertos');
			$table->string('url');
			$table->string('img');
			$table->integer('tipo_id')->unsigned();
			$table->foreign('tipo_id')->references('id')->on('puntos_mapas_tipos');
			$table->smallInteger('estado_codigo')->unsigned();
			$table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('puntos_mapas');
    }
}
