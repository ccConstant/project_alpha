<?php

/*
* Filename : 2022_05_10_063017_create_powers_table.php
* Creation date : 10 May 2022
* Update date : 10 May 2022
* Role : This file is used to create the table "powers" in the data base. In this file, we can see the different
* attribute of this table (name, value, unit...) and how they are defined (string, boolean, unique or not)
*/ 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create the table powers in the data base
     * @return void
     */
    public function up()
    {
        Schema::create('powers', function (Blueprint $table) {
            $table->id();
            $table->string('pow_name') ; 
            $table->string('pow_value') -> nullable(); 
            $table->string('pow_unit') ->nullable(); 
            $table->string('pow_consumptionValue') ->nullable(); 
            $table->string('pow_consumptionUnit') ->nullable(); 
            $table->enum('pow_validate',  ['drafted', 'to_be_validated', 'validated']) ;  
            $table->unsignedBigInteger('enumPowerType_id') ->nullable();
            $table->foreign('enumPowerType_id')->references('id')->on('enum_power_types') -> onDelete('restrict') ; 
            $table->unsignedBigInteger('equipmentTemp_id') ->nullable();
            $table->foreign('equipmentTemp_id')->references('id')->on('equipment_temps') -> onDelete('cascade') ; 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Delete the table powers if it already exist
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('powers');
    }
};

