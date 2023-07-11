<?php

/**
 * Filename: 2023_07_10_140119_create_admin_controls_table.php
 * Creation date: 10 Jul 2023
 * Update date: 10 Jul 2023
 * This file is used to create the table "admin_controls" in the data base. In this file, we can see the different
 * attribute of this table (reference, designation..) and how they are defined (string, boolean, unique or not)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminControlsTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table admin_controls in the database
     * @return void
     */
    public function up()
    {
        Schema::create('admin_controls', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('adminControl_name');
            $table->string('adminControl_reference');
            $table->string('adminControl_materialCertifSpe')->nullable();
            $table->unsignedBigInteger('incmgInsp_id')->nullable();
            $table->foreign('incmgInsp_id')->references('id')->on('incoming_inspections');
            $table->unsignedBigInteger('purSpe_id')->nullable();
            $table->foreign('purSpe_id')->references('id')->on('purchase_specifications');
            $table->string('adminControl_FDS')->nullable();
            /*$table->string('docControl_specDoc')->nullable();*/
        });
    }

     /**
     * Reverse the migrations.
     * Delete the table admin_controls if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_controls');
    }
}

