<?php

/*
* Filename : 2022_05_10_062745_create_preventive_maintenance_operation_realizeds_table.php
* Creation date : 10 May 2022
* Update date : 15 Feb 2023
* This file is used to create the table "preventive_maintenance_operation_realizeds" in the data base. In this file, we can see the different
* attribute of this table (reportNumber, startDate, endDate...) and how they are defined (string, boolean, unique or not)
*/



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create the table preventive_maintenance_operation_realizeds in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('preventive_maintenance_operation_realizeds', function (Blueprint $table) {
            $table->id();
            $table->string('prvMtnOpRlz_reportNumber') ;
            $table->date('prvMtnOpRlz_startDate') ->nullable();
            $table->date('prvMtnOpRlz_endDate') -> nullable();
            $table->date('prvMtnOpRlz_entryDate') ->nullable();
            $table->enum('prvMtnOpRlz_validate',  ['drafted', 'to_be_validated', 'validated']) ;
            $table->unsignedBigInteger('enteredBy_id') -> nullable() ;
            $table->foreign('enteredBy_id')->references('id')->on('users') ->onDelete('restrict')  ;
            $table->unsignedBigInteger('realizedBy_id')  -> nullable() ;
            $table->foreign('realizedBy_id')->references('id')->on('users') ->onDelete('restrict')  ;
            $table->unsignedBigInteger('state_id') ;
            $table->unsignedBigInteger('approvedBy_id')  -> nullable() ;
            $table->foreign('approvedBy_id')->references('id')->on('users') ->onDelete('restrict')  ;
            $table->foreign('state_id')->references('id')->on('states') ->onDelete('restrict')  ;
            $table->unsignedBigInteger('prvMtnOp_id') ;
            $table->foreign('prvMtnOp_id')->references('id')->on('preventive_maintenance_operations') ->onDelete('cascade')  ;
            $table->string('prvMtnOpRlz_comment') ->nullable();
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     *  Delete the table preventive_maintenance_operation_realizeds if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preventive_maintenance_operation_realizeds');
    }
};


