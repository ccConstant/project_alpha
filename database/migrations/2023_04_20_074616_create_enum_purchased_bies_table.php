<?php

/*
* Filename : 2023_04_26_074616_create_enum_purchased_bies_table.php
* Creation date : 26 Apr 2023
* Update date : 26 Apr 2023
* This file is used to create the table "enum_purchased_bies" in the data base. In this file, we can see the different
* attribute of this table (id, value..) and how they are defined (string, boolean, unique or not)
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnumPurchasedBiesTable extends Migration
{
    /**
     * Run the migrations.
     * Create the table enum_purchased_bies in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('enum_purchased_bies', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->boolean('caduc')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table enum_purchased_bies if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enum_purchased_bies');
    }
}
