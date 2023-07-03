<?php

/*
* Filename : 2023_04_20_095009_create_pivot_cons_fam_sto_cond.php
* Creation date : 20 Apr 2023
* Update date : 27 Jun 2023
* This file is used to create the table "pivot_cons_fam_sto_cond" in the data base. In this file, we can see the different
* attribute of this table ((two foreign key for link consFam table and storageCond table)) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotConsFamStoCond extends Migration
{
    /**
     * Run the migrations.
     * Create the table pivot_cons_fam_sto_cond in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_cons_fam_sto_cond', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consFam_id') ;
            $table->foreign('consFam_id')->references('id')->on('cons_families') ->onDelete('cascade') ;
            $table->unsignedBigInteger('storageCondition_id') ;
            $table->foreign('storageCondition_id')->references('id')->on('enum_storage_conditions') ->onDelete('cascade') ;
            $table->enum('validate',  ['drafted', 'to_be_validated', 'validated']) ;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table pivot_cons_fam_sto_cond if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pivot_cons_fam_sto_cond');
    }
}
