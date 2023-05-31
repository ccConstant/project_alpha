<?php

/*
* Filename: 2023_04_20_110425_create_suppliers_table.php
* Creation date: 20 Apr 2023
* Update date: 25 May 2023
* This file is used to create the table "suppliers" in the data base. In this file, we can see the different
* attribute of this table (reference, designation..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table suppliers in the database
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('supplr_name');
            $table->string('supplr_receptionNumber')->nullable();
            $table->string('supplr_formID');

            $table->unsignedBigInteger('supplr_consFam_id')->nullable();
            $table->foreign('supplr_consFam_id')->references('id')->on('cons_families');
            $table->unsignedBigInteger('supplr_compFam_id')->nullable();
            $table->foreign('supplr_compFam_id')->references('id')->on('comp_families');
            $table->unsignedBigInteger('supplr_rawFam_id')->nullable();
            $table->foreign('supplr_rawFam_id')->references('id')->on('raw_families');

            $table->string('supplr_agreementNumber')->nullable();
            $table->string('supplr_qualityCertificationNumber')->nullable();
            $table->string('supplr_specificInstructions')->nullable();
            $table->integer('supplr_version')->default(1);
            $table->unsignedBigInteger('supplr_technicalReviewerId')->nullable();
            $table->foreign('supplr_technicalReviewerId')->references('id')->on('users');
            $table->date('supplr_signatureDate')->nullable();
            $table->enum('supplr_validate', ['drafted', 'to_be_validated', 'validated']);
            $table->string('supplr_siret')->unique()->nullable();
            $table->string('supplr_website')->nullable();
            $table->string('supplr_activity')->nullable();
            $table->boolean('supplr_real')->default(true);
            $table->string('supplr_VATNumber')->unique()->nullable();
            $table->boolean('supplr_critical')->default(false);
            $table->string('supplr_endLinkToFolder')->nullable();
            $table->boolean('supplr_active')->default(true);
            $table->boolean('supplr_concern')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table suppliers if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}
