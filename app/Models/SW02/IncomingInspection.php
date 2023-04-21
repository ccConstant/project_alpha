<?php

/**
 * Filename: IncomingInspection.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file defines the model IncomingInspection. We can see more details about this model (like his attributes) in the
 * migration file named "2023_04_20_115421_create_incoming_inspections_table.php"
 */
namespace App\Models\SW02;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW02\ConsFamily;
use App\Models\SW02\CompFamily;
use App\Models\SW02\RawFamily;
use App\Models\User;

class IncomingInspection extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = ['incmgInsp_remarks', 'incmgInsp_partMaterialComplianceCertificate', 'incmgInsp_rawMaterialCertificate', 'incmgInsp_qualityApproverId', 'incmgInsp_technicalReviewerId', 'incmgInsp_signatureDate', 'incmgInsp_consFam_id', 'incmgInsp_compFam_id', 'incmgInsp_rawFam_id', 'incmgInsp_validate'];

    //Define the relation between a consFamily and its incomingInspection: an incomingInspection can correspond to only one consFamily
    public function cons_family(){
        return $this->belongsTo(ConsFamily::class, 'incmgInsp_consFam_id') ;
    }

    //Define the relation between a compFamily and its incomingInspection: an incomingInspection can correspond to only one compFamily
    public function comp_family(){
        return $this->belongsTo(CompFamily::class, 'incmgInsp_compFam_id') ;
    }

    //Define the relation between a rawFamily and its incomingInspection: an incomingInspection can correspond to only one rawFamily
    public function raw_family(){
        return $this->belongsTo(RawFamily::class, 'incmgInsp_rawFam_id') ;
    }

    //Define the relation between a incomingInspection and the user who reviewed it: an incomingInspection has only one technicalReviewer
    public function technical_reviewer(){
        return $this->belongsTo(User::class, 'incmgInsp_technicalReviewerId') ;
    }

    //Define the relation between a incomingInspection and the user who approved it: an incomingInspection has only one qualityApprover
    public function quality_approver(){
        return $this->belongsTo(User::class, 'incmgInsp_qualityApproverId') ;
    }

    //Define the relation between an incomingInspection and the documentaryControl: an incomingInspection can have multiple documentaryControl
    public function documentary_control(){
        return $this->hasMany(DocumentaryControl::class);
    }

    //Define the relation between an incomingInspection and the aspectTest: an incomingInspection can have multiple aspectTest
    public function aspect_test(){
        return $this->hasMany(AspectTest::class);
    }

    //Define the relation between an incomingInspection and the dimensionalTest: an incomingInspection can have multiple dimensionalTest
    public function dimensional_test(){
        return $this->hasMany(DimensionalTest::class);
    }

    //Define the relation between an incomingInspection and the functionalTest: an incomingInspection can have multiple functionalTest
    public function functional_test(){
        return $this->hasMany(DimensionalTest::class);
    }

    //Define the relation between an incomingInspection and the complementaryTest: an incomingInspection can have multiple complementaryTest
    public function complementary_test(){
        return $this->hasMany(DimensionalTest::class);
    }
}
