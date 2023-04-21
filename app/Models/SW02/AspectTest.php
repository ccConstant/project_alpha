<?php

/**
 * Filename: AspectTest.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file defines the model AspectTest. We can see more details about this model (like his attributes) in the
 * migration file named "2023_04_20_141215_create_aspect_tests_table.php"
 */

namespace App\Models\SW02;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW02\IncomingInspection;

class AspectTest extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = ['aspTest_severityLevel', 'aspTest_levelOfControl', 'aspTest_expectedAspect', 'incmgInsp_id', 'aspTest_name'];

    //Define the relation between an aspectTest and its inspection: an aspectTest has only one inspection
    public function inspection(){
        return $this->belongsTo(IncomingInspection::class, 'incmgInsp_id') ;
    }
}
