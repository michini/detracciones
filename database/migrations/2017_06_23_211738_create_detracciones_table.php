<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetraccionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detracciones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipo');
            $table->string('numero');
            $table->string('razon_social');
            $table->string('nro_proforma');
            $table->string('bien_servicio');
            $table->string('cuenta');
            $table->string('importe');
            $table->string('operacion');
            $table->string('periodo');
            $table->double('comprobante');
            $table->string('serie');
            $table->string('comprobante_numero');
            
            $table->integer('id_proveedor')->unsigned();
            $table->foreign('id_proveedor')->references('id')->on('proveedores')->onDelete('cascade');

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
        Schema::dropIfExists('detracciones');
    }
}
