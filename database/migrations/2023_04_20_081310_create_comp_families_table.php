<?php

/*
* Filename : 2023_04_20_081310_create_comp_families_table.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
* This file is used to create the table "comp_families" in the data base. In this file, we can see the different
* attribute of this table (reference, designation..) and how they are defined (string, boolean, unique or not)
*/ 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table comp_families in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('comp_families', function (Blueprint $table) {
            $table->id('id') ; 
            $table->string('compFam_ref') -> unique ; 
            $table->string('compFam_design') ;
            $table->string('compFam_drawingPath') -> nullable() ; 
            $table->string('compFam_purchasedBy') -> nullable() ; 
            $table->integer('compFam_nbrVersion') -> default(1) ; 
            $table->string('compFam_variablesCharac') -> nullable();  
            $table->timestamps() ; 
            $table->unsignedBigInteger('compFam_qualityApproverId') ->nullable(); 
            $table->foreign('compFam_qualityApproverId')->references('id')->on('users') ;
            $table->unsignedBigInteger('compFam_technicalReviewerId') ->nullable(); 
            $table->foreign('compFam_technicalReviewerId')->references('id')->on('users') ;
            $table->date('compFam_signatureDate') -> nullable() ;
            $table->enum('compFam_validate',  ['drafted', 'to_be_validated', 'validated']) ;  
            $table->string('compFam_version')->nullable();
            $table->boolean('compFam_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table comp_families if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comp_families');
    }
}
