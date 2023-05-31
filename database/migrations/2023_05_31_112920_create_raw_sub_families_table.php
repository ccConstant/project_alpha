<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawSubFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table raw_sub_families in the database
     * @return void
     */
    public function up()
    {
        Schema::create('raw_sub_families', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('rawFam_genDesign')->nullable();
            $table->string('rawFam_genRef')->nullable();
            $table->string('rawFam_drawingPath')->nullable();
            $table->unsignedBigInteger('rawFam_id')->nullable();
            $table->foreign('rawFam_id')->references('id')->on('raw_families');
            $table->string('rawFam_refExtension')->nullable();
            $table->string('rawFam_designExtension')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table raw_sub_families if it already exists
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('raw_sub_families');
    }
}
