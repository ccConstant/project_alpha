<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompSubFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table comp_sub_families in the database
     * @return void
     */
    public function up()
    {
        Schema::create('comp_sub_families', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('compFam_genDesign')->nullable();
            $table->string('compFam_genRef')->nullable();
            $table->string('compFam_drawingPath')->nullable();
            $table->unsignedBigInteger('compFam_id')->nullable();
            $table->foreign('compFam_id')->references('id')->on('comp_families');
            $table->string('compFam_refExtension')->nullable();
            $table->string('compFam_designExtension')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table comp_sub_families if it already exists
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comp_sub_families');
    }
}
