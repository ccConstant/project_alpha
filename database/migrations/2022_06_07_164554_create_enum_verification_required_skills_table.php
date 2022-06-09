<?php

/*
* Filename : 2022_06_07_164554_create_enum_verification_required_skills_table.php
* Creation date : 7 Jun 2022
* Update date : 7 Jun 2022
* Role : This file is used to create the table "enum_usage_metrological_levels" in the data base. In this file, we can see the different
* attribute of this table (id and value) and how they are defined (string, boolean, unique or not)
*/ 


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnumVerificationRequiredSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enum_verification_required_skills', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('value') ; 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enum_verification_required_skills');
    }
}
