<?php

/**
 * Filename: FunctionalTest.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file defines the model FunctionalTest. We can see more details about this model (like his attributes) in the
 * migration file named "2023_04_20_122132_create_functional_tests_table.php"
 */

namespace App\Models\SW03;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\IncomingInspection;

class FunctionalTest extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = ['compSubFam_id', 'consSubFam_id','rawSubFam_id','compFam_id','consFam_id', 'rawFam_id','funcTest_severityLevel', 'funcTest_levelOfControl', 'funcTest_expectedMethod', 'funcTest_expectedValue', 'incmgInsp_id', 'funcTest_name', 'funcTest_unitValue', 'funcTest_desc', 'funcTest_sampling', 'funcTest_specDoc'];

    //Define the relation between a functionalTest and its inspection: a functionalTest has only one inspection
    public function incomingInspection(){
        return $this->belongsTo(IncomingInspection::class, 'incmgInsp_id') ;
    }

    //Define the relation between a functionalTest and his files: a functionalTest has many files
    public function files(){
        return $this->hasMany(File::class, 'funcTest_id') ;
    }
}
