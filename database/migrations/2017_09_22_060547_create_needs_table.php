<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('needs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
			$table->integer('punto_mapa_id')->unsigned();
			$table->foregin('punto_mapa_id')->references('id')->on('puntos_mapas');
			$table->text('descripcion');
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
        Schema::dropIfExists('needs;');
    }
}
