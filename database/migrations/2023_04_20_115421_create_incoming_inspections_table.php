<?php

/**
 * Filename: 2023_04_20_115421_create_incoming_inspections_table.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file is used to create the table "incoming_inspections" in the data base. In this file, we can see the different
 * attribute of this table (reference, designation..) and how they are defined (string, boolean, unique or not)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomingInspectionsTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table incoming_inspections in the database
     * @return void
     */
    public function up()
    {
        Schema::create('incoming_inspections', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('incmgInsp_remarks')->nullable();
            $table->string('incmgInsp_partMaterialComplianceCertificate')->nullable();
            $table->string('incmgInsp_rawMaterialCertificate')->nullable();
            $table->unsignedBigInteger('incmgInsp_qualityApproverId')->nullable();
            $table->foreign('incmgInsp_qualityApproverId')->references('id')->on('users');
            $table->unsignedBigInteger('incmgInsp_technicalReviewerId')->nullable();
            $table->foreign('incmgInsp_technicalReviewerId')->references('id')->on('users');
            $table->date('incmgInsp_signatureDate')->nullable();
            $table->unsignedBigInteger('incmgInsp_consFam_id')->nullable();
            $table->foreign('incmgInsp_consFam_id')->references('id')->on('cons_families');
            $table->unsignedBigInteger('incmgInsp_compFam_id')->nullable();
            $table->foreign('incmgInsp_compFam_id')->references('id')->on('comp_families');
            $table->unsignedBigInteger('incmgInsp_rawFam_id')->nullable();
            $table->foreign('incmgInsp_rawFam_id')->references('id')->on('raw_families');
            $table->enum('incmgInsp_validate',  ['drafted', 'to_be_validated', 'validated']);
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table incoming_inspections if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incoming_inspections');
    }
}
