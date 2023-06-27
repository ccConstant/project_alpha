<?php

/**
 * Filename: 2023_04_20_121202_create_supplier_adrs_table.php
 * Creation date: 20 Apr 2023
 * Update date: 27 Jun 2023
 * This file is used to create the table "supplier_adrs" in the data base. In this file, we can see the different
 * attribute of this table (reference, designation..) and how they are defined (string, boolean, unique or not)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierAdrsTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table supplier_adrs in the database
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_adrs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('supplrAdr_street')->nullable();
            $table->string('supplrAdr_town')->nullable();
            $table->string('supplrAdr_country')->nullable();
            $table->unsignedBigInteger('supplr_id');
            $table->foreign('supplr_id')->references('id')->on('suppliers');
            $table->enum('supplrAdr_validate',  ['drafted', 'to_be_validated', 'validated']);
            $table->string('supplrAdr_name');
            $table->boolean('supplrAdr_principal')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_adrs');
    }
}
