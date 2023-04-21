<?php

/**
 * Filename: 2023_04_20_123220_create_complementary_tests_table.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file is used to create the table "complementary_tests" in the data base. In this file, we can see the different
 * attribute of this table (reference, designation..) and how they are defined (string, boolean, unique or not)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplementaryTestsTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table complementary_tests in the database
     * @return void
     */
    public function up()
    {
        Schema::create('complementary_tests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('compTest_name');
            $table->string('compTest_unitValue');
            $table->double('compTest_expectedValue');
            $table->enum('compTest_severityLevel', ['I', 'II', 'III', 'IV']);
            $table->enum('compTest_levelOfControl', ['Reduced', 'Normal', 'Reinforced']);
            $table->string('compTest_expectedMethod');
            $table->unsignedBigInteger('incmgInsp_id');
            $table->foreign('incmgInsp_id')->references('id')->on('incoming_inspections');
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table complementary_tests if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complementary_tests');
    }
}
