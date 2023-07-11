<?php

/**
 * Filename: 2023_04_20_141240_create_documentary_controls_table.php
 * Creation date: 20 Apr 2023
 * Update date: 11 Jul 2023
 * This file is used to create the table "documentary_controls" in the data base. In this file, we can see the different
 * attribute of this table (reference, designation..) and how they are defined (string, boolean, unique or not)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentaryControlsTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table documentary_controls in the database
     * @return void
     */
    public function up()
    {
        Schema::create('documentary_controls', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('docControl_name');
            $table->string('docControl_reference');
            $table->string('docControl_materialCertifSpe')->nullable();
            $table->unsignedBigInteger('incmgInsp_id')->nullable();
            $table->foreign('incmgInsp_id')->references('id')->on('incoming_inspections');
            $table->unsignedBigInteger('purSpe_id')->nullable();
            $table->foreign('purSpe_id')->references('id')->on('purchase_specifications');
            $table->string('docControl_FDS')->nullable();
            /*$table->string('docControl_specDoc')->nullable();*/
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table complementary_tests if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentary_controls');
    }
}
