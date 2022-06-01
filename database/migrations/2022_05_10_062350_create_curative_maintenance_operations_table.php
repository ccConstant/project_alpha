<?php

/*
* Filename : 2022_05_10_062350_create_curative_maintenance_operations_table.php
* Creation date : 10 May 2022
* Update date : 10 May 2022
* Role : This file is used to create the table "curative_maintenance_operations" in the data base. In this file, we can see the different
* attribute of this table (number, reportNumber, description...) and how they are defined (string, boolean, unique or not)
*/ 


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create the table curative_maintenance_operations in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('curative_maintenance_operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('curMtnOp_number') ; 
            $table->string('curMtnOp_reportNumber') ->nullable() ; 
            $table->mediumText('curMtnOp_description') -> nullable(); 
            $table->string('curMtnOp_startDate') ; 
            $table->string('curMtnOp_endDate') ->nullable(); 
            $table->enum('curMtnOp_validate', ['DRAFTED', 'TO_BE_VALIDATED', 'VALIDATED']) ;  
            $table->unsignedBigInteger('state_id') ;
            $table->foreign('state_id')->references('id')->on('states') ->onDelete('cascade')  ; 
            $table->unsignedBigInteger('qualityVerifier_id') -> nullable() ; 
            $table->foreign('qualityVerifier_id')->references('id')->on('peoples') ->onDelete('restrict')  ; 
            $table->unsignedBigInteger('technicalVerifier_id')  -> nullable() ; 
            $table->foreign('technicalVerifier_id')->references('id')->on('peoples') ->onDelete('restrict')  ; 
            $table->unsignedBigInteger('realizedBy_id') -> nullable() ; 
            $table->foreign('realizedBy_id')->references('id')->on('peoples') ->onDelete('restrict')  ;
            $table->unsignedBigInteger('enteredBy_id') -> nullable() ; 
            $table->foreign('enteredBy_id')->references('id')->on('peoples') ->onDelete('restrict')  ;  

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table curative_maintenance_operations if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('curative_maintenance_operations');
    }
};

