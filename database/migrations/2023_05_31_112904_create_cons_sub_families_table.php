<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsSubFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table cons_sub_families in the database
     * @return void
     */
    public function up()
    {
        Schema::create('cons_sub_families', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('consFam_genDesign')->nullable();
            $table->string('consFam_genRef')->nullable();
            $table->string('consFam_drawingPath')->nullable();
            $table->unsignedBigInteger('consFam_id')->nullable();
            $table->foreign('consFam_id')->references('id')->on('cons_families');
            $table->string('consFam_refExtension')->nullable();
            $table->string('consFam_designExtension')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table cons_sub_families if it already exists
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cons_sub_families');
    }
}
