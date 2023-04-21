<?php

/**
 * Filename: FunctionalTest.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file defines the model FunctionalTest. We can see more details about this model (like his attributes) in the
 * migration file named "2023_04_20_122132_create_functional_tests_table.php"
 */

namespace App\Models\SW02;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunctionalTest extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = ['funcTest_severityLevel', 'funcTest_levelOfControl', 'funcTest_expectedMethod', 'funcTest_expectedValue', 'incmInsp_id', 'funcTest_name', 'funcTest_unitValue'];

    //Define the relation between a functionalTest and its inspection: a functionalTest has only one inspection
    public function inspection(){
        return $this->belongsTo(IncomingInspection::class, 'incmInsp_id') ;
    }
}
