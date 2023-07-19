<?php

/*
* Filename : 2023_07_18_091228_pivot_cons_sub_fam_sto_cond.php
* Creation date : 17 Jul 2023
* Update date : 17 Jul 2023
* This file is used to create the table "pivot_cons_sub_fam_sto_cond" in the data base. In this file, we can see the different
* attribute of this table ((two foreign key for link consSubFam table and storageCond table)) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PivotConsSubFamStoCond extends Migration
{
    /**
     * Run the migrations.
     * Create the table pivot_cons_sub_fam_sto_cond in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_cons_sub_fam_sto_cond', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consSubFam_id') ;
            $table->foreign('consSubFam_id')->references('id')->on('cons_sub_families') ->onDelete('cascade') ;
            $table->unsignedBigInteger('storageCondition_id') ;
            $table->foreign('storageCondition_id')->references('id')->on('enum_storage_conditions') ->onDelete('cascade') ;
            $table->enum('validate',  ['drafted', 'to_be_validated', 'validated']) ;
            $table->timestamps();
        });
    }

     /**
     * Reverse the migrations.
     * Delete the table pivot_cons_sub_fam_sto_cond if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pivot_cons_sub_fam_sto_cond');
    }
}
