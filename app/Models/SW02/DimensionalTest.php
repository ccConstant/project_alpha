<?php

/**
 * Filename: DimensionalTest.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file defines the model DimensionalTest. We can see more details about this model (like his attributes) in the
 * migration file named "2023_04_20_122832_create_dimensional_tests_table.php"
 */

namespace App\Models\SW02;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimensionalTest extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = ['dimTest_severityLevel', 'dimTest_levelOfControl', 'dimTest_expectedMethod', 'dimTest_expectedValue', 'incmInsp_id', 'dimTest_name', 'dimTest_unitValue'];

    //Define the relation between a dimensionalTest and its inspection: a dimensionalTest has only one inspection
    public function inspection(){
        return $this->belongsTo(IncomingInspection::class, 'incmInsp_id') ;
    }
}
