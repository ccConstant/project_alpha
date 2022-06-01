<?php

/*
* Filename : 2022_05_10_064422_create_files_table.php
* Creation date : 10 May 2022
* Update date : 19 May 2022
* Role : This file is used to create the table "files" in the data base. In this file, we can see the different
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
            $table->enum('file_validate', ['DRAFTED', 'TO_BE_VALIDATED', 'VALIDATED']) ;  
            $table->unsignedBigInteger('equipmentTemp_id') ->nullable();
            $table->foreign('equipmentTemp_id')->references('id')->on('equipment_temps') -> onDelete('cascade') ; 
            $table->timestamps();
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


