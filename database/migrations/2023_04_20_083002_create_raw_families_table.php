<?php

/*
* Filename : 2023_04_20_083002_create_raw_families_table.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
* This file is used to create the table "raw_families" in the data base. In this file, we can see the different
* attribute of this table (reference, designation..) and how they are defined (string, boolean, unique or not)
*/ 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table raw_families in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('raw_families', function (Blueprint $table) {
            $table->id('id') ; 
            $table->string('rawFam_ref') -> unique ; 
            $table->string('rawFam_design') ;
            $table->string('rawFam_drawingPath') -> nullable() ; 
            $table->string('rawFam_purchasedBy') -> nullable() ; 
            $table->integer('rawFam_nbrVersion') -> default(1) ; 
            $table->string('rawFam_variablesCharac') -> nullable();  
            $table->timestamps() ; 
            $table->unsignedBigInteger('rawFam_qualityApproverId') ->nullable(); 
            $table->foreign('rawFam_qualityApproverId')->references('id')->on('users') ;
            $table->unsignedBigInteger('rawFam_technicalReviewerId') ->nullable(); 
            $table->foreign('rawFam_technicalReviewerId')->references('id')->on('users') ;
            $table->date('rawFam_signatureDate') -> nullable() ;
            $table->enum('rawFam_validate',  ['drafted', 'to_be_validated', 'validated']) ;  
            $table->boolean('rawFam_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table raw_families if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('raw_families');
    }
}
