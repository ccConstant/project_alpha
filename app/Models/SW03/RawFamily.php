<?php

/*
* Filename : RawFamily.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
* This file define the model RawFamily. We can see more details about this model (like his attributes) in the
* migration file named "2023_04_20_083002_create_raw_families_table.php"
*
*/

namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\SW03\EnumStorageCondition;
use App\Models\SW03\Supplier;
use App\Models\SW03\RawFamilyMember;
use App\Models\SW03\Criticality;
use App\Models\SW03\EnumPurchasedBy;



class RawFamily extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = [
        'rawFam_ref',
        'rawFam_design',
        'rawFam_drawingPath',
        'enumPurchasedBy_id',
        'rawFam_nbrVersion',
        'rawFam_variablesCharac',
        'rawFam_variablesCharacDesign',
        'rawFam_qualityApproverId',
        'rawFam_technicalReviewerId',
        'rawFam_signatureDate',
        'rawFam_validate',
        'rawFam_active',
        'rawFam_genDesign',
        'rawFam_genRef',
        'rawFam_subFam',
        'rawFam_version',
        'rawFam_materials',
        'rawFam_specifications', 
        'rawFam_documentsRequested'
    ] ;

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

     //Define the relation between an EnumStorageCondition and its rawFamily : a rawFamily can correspond to many EnumStorageCondition
     public function storage_conditions(){
        return $this->belongsToMany(EnumStorageCondition::class, 'pivot_raw_fam_sto_cond', 'rawFam_id', 'storageCondition_id') ;
    }

     //Define the relation between a supplier and its rawFamily : a rawFamily can correspond to many suppliers
     public function suppliers(){
        return $this->belongsToMany(Supplier::class, 'pivot_raw_fam_supplr', 'rawFam_id', 'supplr_id') ;
    }

     //Define the relation between a rawFamily and its criticality : a rawFamily has only one criticality
     public function criticality(){
        return $this->HasOne(Criticality::class) ;
    }

    //Define the relation between an EnumPurchasedBy and its rawFamily : a rawFamily can correspond to many EnumPurchasedBy
    public function purchased_by(){
        return $this->belongsTo(EnumPurchasedBy::class, 'enumPurchasedBy_id') ;
    }
}
