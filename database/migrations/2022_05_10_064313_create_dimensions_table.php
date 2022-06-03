<?php

/*
* Filename : 2022_05_10_064313_create_dimensions_table.php
* Creation date : 10 May 2022
* Update date : 10 May 2022
* Role : This file is used to create the table "dimensions" in the data base. In this file, we can see the different
* attribute of this table (value, validate) and how they are defined (string, boolean, unique or not)
*/ 



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create the table dimensions in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('dimensions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->tinyText('dim_value') ; 
            $table->enum('dim_validate', ['drafted', 'to_be_validated', 'validated']) ;  
            $table->unsignedBigInteger('enumDimensionType_id') ->nullable();
            $table->foreign('enumDimensionType_id')->references('id')->on('enum_dimension_types') -> onDelete('restrict') ;
            $table->unsignedBigInteger('enumDimensionName_id') ->nullable();
            $table->foreign('enumDimensionName_id')->references('id')->on('enum_dimension_names') -> onDelete('restrict') ;
            $table->unsignedBigInteger('enumDimensionUnit_id') ->nullable();
            $table->foreign('enumDimensionUnit_id')->references('id')->on('enum_dimension_units') -> onDelete('restrict') ;
            $table->unsignedBigInteger('equipmentTemp_id') ->nullable();
            $table->foreign('equipmentTemp_id')->references('id')->on('equipment_temps') -> onDelete('cascade') ; 
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table dimensions if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dimensions');
    }
};

