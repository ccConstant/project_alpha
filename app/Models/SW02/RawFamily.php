<?php

/*
* Filename : RawFamily.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
* This file define the model RawFamily. We can see more details about this model (like his attributes) in the 
* migration file named "2023_04_20_083002_create_raw_families_table.php" 
* 
*/ 

namespace App\Models\SW02;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RawFamilyMember;
use App\Models\User;

class RawFamily extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['rawFam_ref', 'rawFam_design', 'rawFam_drawingPath', 'rawFam_purchasedBy', 'rawFam_nbrVersion', 'rawFam_variablesCharac', 'rawFam_qualityApproverId', 'rawFam_technicalReviewerId', 'rawFam_signatureDate', 'rawFam_validate', 'rawFam_active'] ;

    //Define the relation between a rawFamily and its rawFamilyMember : a rawFamilyMember can correspond to only one rawFamily
    public function raw_family_member(){
        return $this->hasMany(RawFamilyMember::class) ; 
    }

    //Define the relation between a rawFamily and the user who approved it : a rawFamily has only one qualityApprover
    public function quality_approver(){
        return $this->belongsTo(User::class, 'rawFam_qualityApproverId') ; 
    }

     //Define the relation between a rawFamily and the user who reviewed it : a rawFamily has only one technicalReviewer
     public function technical_reviewer(){
        return $this->belongsTo(User::class, 'rawFam_technicalReviewerId') ; 
    }
}
