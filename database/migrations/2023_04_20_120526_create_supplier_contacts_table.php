<?php

/*
 * Filename: 2023_04_20_120526_create_supplier_contacts_table.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file is used to create the table "supplier_contacts" in the data base. In this file, we can see the different
 * attribute of this table (reference, designation..) and how they are defined (string, boolean, unique or not)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierContactsTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table supplier_contacts in the database
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_contacts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('supplrContact_name');
            $table->string('supplrContact_function')->nullable();
            $table->string('supplrContact_phoneNumber')->nullable();
            $table->string('supplrContact_email')->nullable();
            $table->unsignedBigInteger('supplr_id');
            $table->foreign('supplr_id')->references('id')->on('suppliers');
            $table->enum('supplrContact_validate',  ['drafted', 'to_be_validated', 'validated']);
            $table->boolean('supplrContact_principal')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table supplier_contacts if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_contacts');
    }
}
