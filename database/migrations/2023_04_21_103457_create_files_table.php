<?php

/*
* Filename : 2022_06_07_064422_create_files_table.php
* Creation date : 10 May 2022
* Update date : 27 Jun 2023
* This file is used to create the table "files" in the data base. In this file, we can see the different
* attribute of this table (name, location, validate..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create the table files in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('file_name') ;
            $table->string('file_location') -> nullable();
            $table->enum('file_validate',  ['drafted', 'to_be_validated', 'validated']) ;
            $table->unsignedBigInteger('equipmentTemp_id') ->nullable();
            $table->foreign('equipmentTemp_id')->references('id')->on('equipment_temps') -> onDelete('cascade') ;
            $table->unsignedBigInteger('mmeTemp_id') ->nullable();
            $table->foreign('mmeTemp_id')->references('id')->on('mme_temps') -> onDelete('cascade') ;
            $table->timestamps();
            $table->unsignedBigInteger('documentaryControl_id') ->nullable();
            $table->foreign('documentaryControl_id')->references('id')->on('documentary_controls')->onDelete('cascade');
            $table->unsignedBigInteger('aspectTest_id') ->nullable();
            $table->foreign('aspectTest_id')->references('id')->on('aspect_tests')->onDelete('cascade');
            $table->unsignedBigInteger('functionalTest_id') ->nullable();
            $table->foreign('functionalTest_id')->references('id')->on('functional_tests')->onDelete('cascade');
            $table->unsignedBigInteger('dimensionalTest_id') ->nullable();
            $table->foreign('dimensionalTest_id')->references('id')->on('dimensional_tests')->onDelete('cascade');
            $table->unsignedBigInteger('complementaryTest_id') ->nullable();
            $table->foreign('complementaryTest_id')->references('id')->on('complementary_tests')->onDelete('cascade');
            $table->unsignedBigInteger('purchaseSpec_id') ->nullable();
            $table->foreign('purchaseSpec_id')->references('id')->on('purchase_specifications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table files if it already exist
     * @return void
     */
    public function down(){
        Schema::dropIfExists('files');
    }
};


