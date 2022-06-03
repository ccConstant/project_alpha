<?php

/*
* Filename : 2022_05_10_062625_create_preventive_maintenance_operations_table.php
* Creation date : 10 May 2022
* Update date : 10 May 2022
* Role : This file is used to create the table "preventive_maintenance_operations" in the data base. In this file, we can see the different
* attribute of this table (number, description, periodicity...) and how they are defined (string, boolean, unique or not)
*/ 



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create the preventive_maintenance_operations in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('preventive_maintenance_operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('prvMtnOp_number')  ; 
            $table->string('prvMtnOp_description') ; 
            $table->unsignedMediumInteger('prvMtnOp_periodicity') ->nullable(); 
            $table->enum('prvMtnOp_symbolPeriodicity', ['Y', 'M', 'D', 'H']) ->nullable();  
            $table->mediumText('prvMtnOp_protocol') ->nullable(); 
            $table->timestamp('prvMtnOp_startDate') ; 
            $table->timestamp('prvMtnOp_nextDate') ->nullable(); 
            $table->date('prvMtnOp_reformDate') ->nullable() ; 
            $table->enum('prvMtnOp_validate',  ['drafted', 'to_be_validated', 'validated']) ;  
            $table->unsignedBigInteger('equipmentTemp_id') ->nullable() ;
            $table->foreign('equipmentTemp_id')->references('id')->on('equipment_temps') ->onDelete('cascade')  ;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete preventive_maintenance_operations table if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preventive_maintenance_operations');
    }
};




