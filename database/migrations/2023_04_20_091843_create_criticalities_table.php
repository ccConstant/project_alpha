<?php

/*
* Filename : 2023_04_20_091843_create_criticalities_table.php
* Creation date : 20 Apr 2023
* Update date : 26 Apr 2023
* This file is used to create the table "comp_criticalities" in the data base. In this file, we can see the different
* attribute of this table (compCriticality, technicalReviewer..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCriticalitiesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table comp_criticalities in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('criticalities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('crit_artCriticality',  ['NOT_CRITICAL', 'DETECTABLE', 'CRITICAL']) ;
            $table->enum('crit_artMaterialContactCriticality',  ['NOT_CRITICAL', 'DETECTABLE', 'CRITICAL']) ;
            $table->enum('crit_artMaterialFunctionCriticality',  ['NOT_CRITICAL', 'DETECTABLE', 'CRITICAL']) ;
            $table->enum('crit_artProcessCriticality',  ['NOT_CRITICAL', 'DETECTABLE', 'CRITICAL']) ;
            $table->unsignedBigInteger('crit_qualityApproverId') ->nullable();
            $table->foreign('crit_qualityApproverId')->references('id')->on('users') ;
            $table->unsignedBigInteger('crit_technicalReviewerId') ->nullable();
            $table->foreign('crit_technicalReviewerId')->references('id')->on('users') ;
            $table->date('crit_signatureDate') -> nullable() ;
            $table->enum('crit_validate',  ['drafted', 'to_be_validated', 'validated']) ;
            $table->string('crit_remarks') ->nullable();
            $table->unsignedBigInteger('compFam_id') ->nullable();
            $table->foreign('compFam_id')->references('id')->on('comp_families') ;
            $table->unsignedBigInteger('consFam_id') ->nullable();
            $table->foreign('consFam_id')->references('id')->on('cons_families') ;
            $table->unsignedBigInteger('rawFam_id') ->nullable();
            $table->foreign('rawFam_id')->references('id')->on('raw_families') ;
            $table->string('crit_justification')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table comp_criticalities if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('criticalities');
    }
}
