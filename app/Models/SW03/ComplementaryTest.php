<?php

/**
 * Filename: ComplementaryTest.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file defines the model ComplementaryTest. We can see more details about this model (like his attributes) in the
 * migration file named "2023_04_20_123220_create_complementary_tests_table.php"
 */

namespace App\Models\SW03;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\IncomingInspection;

class ComplementaryTest extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = ['compTest_name', 'compTest_unitValue', 'compTest_expectedValue', 'compTest_severityLevel', 'compTest_levelOfControl', 'compTest_expectedMethod', 'incmgInsp_id'];

    //Define the relation between a complementaryTest and its inspection: a complementaryTest has only one inspection
    public function incomingInspection(){
        return $this->belongsTo(IncomingInspection::class, 'incmgInsp_id') ;
    }

    //Define the relation between a complementaryTest and his files: a complementaryTest has many files
    public function files(){
        return $this->hasMany(File::class, 'compTest_id') ;
    }
}
