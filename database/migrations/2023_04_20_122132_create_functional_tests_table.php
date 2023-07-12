<?php

/**
 * Filename: 2023_04_20_122132_create_functional_tests_table.php
 * Creation date: 20 Apr 2023
 * Update date: 11 Jul 2023
 * This file is used to create the table "functional_tests" in the data base. In this file, we can see the different
 * attribute of this table (reference, designation..) and how they are defined (string, boolean, unique or not)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFunctionalTestsTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table functional_tests in the database
     * @return void
     */
    public function up()
    {
        Schema::create('functional_tests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('funcTest_severityLevel', ['I', 'II', 'III', 'IV'])->nullable();
            $table->enum('funcTest_levelOfControl', ['Reduced', 'Normal', 'Reinforced'])->nullable();
            $table->string('funcTest_expectedMethod');
            $table->string('funcTest_expectedValue');
            $table->unsignedBigInteger('incmgInsp_id')->nullable();
            $table->foreign('incmgInsp_id')->references('id')->on('incoming_inspections');
            
            $table->unsignedBigInteger('compSubFam_id') ->nullable();
            $table->foreign('compSubFam_id')->references('id')->on('comp_sub_families') ;
            $table->unsignedBigInteger('consSubFam_id') ->nullable();
            $table->foreign('consSubFam_id')->references('id')->on('cons_sub_families') ;
            $table->unsignedBigInteger('rawSubFam_id') ->nullable();
            $table->foreign('rawSubFam_id')->references('id')->on('raw_sub_families') ;
            $table->unsignedBigInteger('compFam_id') ->nullable();
            $table->foreign('compFam_id')->references('id')->on('comp_families') ;
            $table->unsignedBigInteger('consFam_id') ->nullable();
            $table->foreign('consFam_id')->references('id')->on('cons_families') ;
            $table->unsignedBigInteger('rawFam_id') ->nullable();
            $table->foreign('rawFam_id')->references('id')->on('raw_families') ;

            $table->string('funcTest_name');
            $table->enum('funcTest_sampling',  ['Statistics', '100%', 'Other']);
            $table->string('funcTest_desc')->nullable();
            $table->string('funcTest_specDoc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table functional_tests if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('functional_tests');
    }
}
