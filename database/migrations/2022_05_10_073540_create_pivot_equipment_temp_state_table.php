<?php

/*
* Filename : 2022_05_10_073540_create_pivot_equipment_temp_state_table.php
* Creation date : 10 May 2022
* Update date : 15 Feb 2023
* This file is used to create the table "pivot_equipment_temp_state" in the data base. In this file, we can see the different
* attribute of this table (two foreign key for link state table and equipment_temp table) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Create the table pivot_equipment_temp_state in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_equipment_temp_state', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('equipmentTemp_id');
            $table->foreign('equipmentTemp_id')->references('id')->on('equipment_temps')->onDelete('cascade');
            $table->unsignedBigInteger('state_id');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table pivot_equipment_temp_state if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pivot_equipment_temp_state');
    }
};


