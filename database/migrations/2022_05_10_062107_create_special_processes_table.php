<?php

/*
* Filename : 2022_05_10_062107_create_special_processes_table.php
* Creation date : 10 May 2022
* Update date : 27 Jun 2023
* This file is used to create the table "special_processes" in the data base. In this file, we can see the different
* attribute of this table (remarksOrPrecaution, name, exist..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create the table special_processes in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('special_processes', function (Blueprint $table) {
            $table->id();
            $table->boolean('spProc_exist') ;
            $table->MediumText('spProc_remarksOrPrecaution') ->nullable() ;
            $table->enum('spProc_validate',  ['drafted', 'to_be_validated', 'validated']) ;
            $table->string('spProc_name') ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table special_processes if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('special_processes');
    }
};


