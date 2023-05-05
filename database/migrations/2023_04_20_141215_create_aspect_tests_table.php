<?php

/**
 * Filename: 2023_04_20_141215_create_aspect_tests_table.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file is used to create the table "aspect_tests" in the data base. In this file, we can see the different
 * attribute of this table (reference, designation..) and how they are defined (string, boolean, unique or not)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAspectTestsTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table aspect_tests in the database
     * @return void
     */
    public function up()
    {
        Schema::create('aspect_tests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('aspTest_severityLevel', ['I', 'II', 'III', 'IV'])->nullable();
            $table->enum('aspTest_levelOfControl', ['Reduced', 'Normal', 'Reinforced'])->nullable();
            $table->string('aspTest_expectedAspect');
            $table->unsignedBigInteger('incmgInsp_id');
            $table->foreign('incmgInsp_id')->references('id')->on('incoming_inspections');
            $table->string('aspTest_name');
            $table->enum('aspTest_sampling',  ['Statistics', '100%', 'Other']);
            $table->string('aspTest_desc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table complementary_tests if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aspect_tests');
    }
}
