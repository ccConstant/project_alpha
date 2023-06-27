<?php

/*
* Filename : 2023_01_18_073912_create_histories_table.php
* Creation date : 18 Jan 2023
* Update date : 27 Jun 2023
* This file is used to create the table "histories" in the data base. In this file, we can see the different
* attribute of this table (numVersion, mme_id, equipment_id...) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriesTable extends Migration
{


    /**
     * Run the migrations.
     * Create the table histories in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('history_numVersion');
            $table->string('history_reasonUpdate') ;
            $table->unsignedBigInteger('mmeTemp_id') ->nullable() ;
            $table->foreign('mmeTemp_id')->references('id')->on('mme_temps') ->onDelete('cascade')  ;
            $table->unsignedBigInteger('equipmentTemp_id') ->nullable();
            $table->foreign('equipmentTemp_id')->references('id')->on('equipment_temps') -> onDelete('cascade') ;
            $table->unsignedBigInteger('compFam_id') ->nullable();
            $table->foreign('compFam_id')->references('id')->on('comp_families') -> onDelete('cascade') ;
            $table->unsignedBigInteger('consFam_id') ->nullable();
            $table->foreign('consFam_id')->references('id')->on('cons_families') -> onDelete('cascade') ;
            $table->unsignedBigInteger('rawFam_id') ->nullable();
            $table->foreign('rawFam_id')->references('id')->on('raw_families') -> onDelete('cascade') ;
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     * Delete the table histories if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('histories');
    }
}




