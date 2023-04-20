<?php

/*
* Filename : 2023_04_20_091843_create_comp_criticalities_table.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
* This file is used to create the table "comp_criticalities" in the data base. In this file, we can see the different
* attribute of this table (compCriticality, technicalReviewer..) and how they are defined (string, boolean, unique or not)
*/ 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompCriticalitiesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table comp_criticalities in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('comp_criticalities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('compCrit_compCriticality',  ['NOT_CRITICAL', 'DETECTABLE', 'CRTICAL']) ;
            $table->enum('compCrit_compMaterialContactCriticality',  ['NOT_CRITICAL', 'DETECTABLE', 'CRTICAL']) ;
            $table->enum('compCrit_compMaterialFunctionCriticality',  ['NOT_CRITICAL', 'DETECTABLE', 'CRTICAL']) ;
            $table->enum('compCrit_compProcessCriticality',  ['NOT_CRITICAL', 'DETECTABLE', 'CRTICAL']) ;
            $table->unsignedBigInteger('compCrit_qualityApproverId') ->nullable(); 
            $table->foreign('compCrit_qualityApproverId')->references('id')->on('users') ;
            $table->unsignedBigInteger('compCrit_technicalReviewerId') ->nullable(); 
            $table->foreign('compCrit_technicalReviewerId')->references('id')->on('users') ;
            $table->date('compCrit_signatureDate') -> nullable() ;
            $table->enum('compCrit_validate',  ['drafted', 'to_be_validated', 'validated']) ;
            $table->string('compCrit_remarks') ->nullable();
            $table->unsignedBigInteger('compFam_id') ->nullable();
            $table->foreign('compFam_id')->references('id')->on('comp_families') ;
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table comp_criticalities if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comp_criticalities');
    }
}
