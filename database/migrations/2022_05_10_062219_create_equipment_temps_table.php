<?php

/*
* Filename : 2022_05_10_062219_create_equipment_temps_table.php
* Creation date : 10 May 2022
* Update date : 15 Feb 2023
* This file is used to create the table "equipement_temps" in the data base. In this file, we can see the different
* attribute of this table (version, date, validate..) and how they are defined (string, boolean, unique or not)
*/


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Class CreateEquipmentTempsTable extends Migration{
    /**
     * Run the migrations.
     * Create the table equipment_temps in the data base
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_temps', function (Blueprint $table) {
            $table->id() ;
            $table->unsignedTinyInteger('eqTemp_version') ;
            $table->timestamp('eqTemp_date') ;
            $table->string('eqTemp_location')->nullable();
            $table->enum('eqTemp_validate',  ['drafted', 'to_be_validated', 'validated']) ;
            $table->timestamps();
            $table->boolean('eqTemp_lifeSheetCreated') ->default(false) ;
            $table->double('eqTemp_mass') -> nullable() ;
            $table->text('eqTemp_remarks') -> nullable();
            $table->boolean('eqTemp_mobility') ->nullable() ;
            $table->unsignedBigInteger('enumType_id') ->nullable();
            $table->foreign('enumType_id')->references('id')->on('enum_equipment_types') -> onDelete('restrict') ;
            $table->unsignedBigInteger('enumMassUnit_id') ->nullable();
            $table->foreign('enumMassUnit_id')->references('id')->on('enum_equipment_mass_units') -> onDelete('restrict') ;
            $table->unsignedBigInteger('equipment_id') ;
            $table->foreign('equipment_id')->references('id')->on('equipment') ->onDelete('cascade')  ;
            $table->unsignedBigInteger('qualityVerifier_id') -> nullable() ;
            $table->foreign('qualityVerifier_id')->references('id')->on('users') ->onDelete('restrict')  ;
            $table->unsignedBigInteger('technicalVerifier_id')  -> nullable() ;
            $table->foreign('technicalVerifier_id')->references('id')->on('users') ->onDelete('restrict')  ;
            $table->unsignedBigInteger('createdBy_id') -> nullable() ;
            $table->foreign('createdBy_id')->references('id')->on('users') ->onDelete('restrict')  ;
            $table->unsignedBigInteger('specialProcess_id') -> nullable() ;
            $table->foreign('specialProcess_id')->references('id')->on('special_processes') ;
            $table->date('eqTemp_signatureDate')->nullable();


        });
    }

        /**
     * Reverse the migrations
     * Delete the table equipment_temps if it already exist
     * @return void
     */
    public function down(){
        Schema::dropIfExists('equipment_temps');
    }

};

