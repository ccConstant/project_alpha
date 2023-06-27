<?php

/*
* Filename : 2022_06_21_132054_create_pivot_mme_temp_state_table.php
* Creation date : 21 Jun 2022
* Update date : 27 Jun 2023
* This file is used to create the table "pivot_mme_temp_state" in the data base. In this file, we can see the different
* attribute of this table (two foreign key for link state table and mme_temp table) and how they are defined (string, boolean, unique or not)
*/


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotMmeTempStateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_mme_temp_state', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mmeTemp_id') ;
            $table->foreign('mmeTemp_id')->references('id')->on('mme_temps') ->onDelete('cascade') ;
            $table->unsignedBigInteger('mme_state_id') ;
            $table->foreign('mme_state_id')->references('id')->on('mme_states') ->onDelete('cascade') ;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pivot_mme_temp_state');
    }
}
