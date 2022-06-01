<?php

/*
* Filename : 2022_05_10_063805_create_risks_table.php
* Creation date : 10 May 2022
* Update date : 10 May 2022
* Role : This file is used to create the table "risks" in the data base. In this file, we can see the different
* attribute of this table (remarks, wayOfControl...) and how they are defined (string, boolean, unique or not)
*/ 


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create the table risks in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->mediumText('risk_remarks') -> nullable(); 
            $table->mediumText('risk_wayOfControl') ->nullable(); 
            $table->enum('risk_validate', ['DRAFTED', 'TO_BE_VALIDATED', 'VALIDATED']) -> nullable(); 
            $table->unsignedBigInteger('enumRiskFor_id') ->nullable();
            $table->foreign('enumRiskFor_id')->references('id')->on('enum_risk_fors') -> onDelete('restrict') ; 
            $table->unsignedBigInteger('equipmentTemp_id') ->nullable();
            $table->foreign('equipmentTemp_id')->references('id')->on('equipment_temps') -> onDelete('cascade') ; 
            $table->unsignedBigInteger('preventiveMaintenanceOperation_id') ->nullable();
            $table->foreign('preventiveMaintenanceOperation_id')->references('id')->on('preventive_maintenance_operations') -> onDelete('cascade') ; 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table risks if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('risks');
    }
};



