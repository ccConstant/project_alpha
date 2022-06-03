<?php


/*
* Filename : 2022_05_10_063905_create_usages_table.php
* Creation date : 9 May 2022
* Update date : 10 May 2022
* Role : This file is used to create the table "usages" in the data base. In this file, we can see the different
* attribute of this table (type, precaution...) and how they are defined (string, boolean, unique or not)
*/ 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create the table usages in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('usages', function (Blueprint $table) {
            $table->id();
            $table->string('usg_type') ; 
            $table->MediumText('usg_precaution') ->nullable(); 
            $table->date('usg_startDate') ; 
            $table->date('usg_reformDate') -> nullable(); 
            $table->enum('usg_validate',  ['drafted', 'to_be_validated', 'validated']) ;  
            $table->unsignedBigInteger('equipmentTemp_id') ->nullable();
            $table->foreign('equipmentTemp_id')->references('id')->on('equipment_temps') -> onDelete('cascade') ; 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table usages if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usages');
    }
};

