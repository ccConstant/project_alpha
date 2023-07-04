<?php

/*
* Filename : CompFamily.php
* Creation date : 20 Apr 2023
* Update date : 3 Jul 2023
* This file define the model CompFamily. We can see more details about this model (like his attributes) in the
* migration file named "2023_04_20_081310_create_comp_families_table.php" in Database>migrations."
*
*/


namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\CompFamilyMember;
use App\Models\SW03\CompSubFamily;
use App\Models\User;
use App\Models\SW03\EnumStorageCondition;
use App\Models\SW03\Supplier;
use App\Models\SW03\Criticality;
use App\Models\SW03\EnumPurchasedBy;

class CompFamily extends Model
{
    use HasFactory;

     //Data which can be added, updated or deleted by us in the data base.
     protected $fillable = ['compFam_ref',
         'compFam_design',
         'compFam_drawingPath',
         'enumPurchasedBy_id',
         'compFam_nbrVersion',
         'compFam_variablesCharac',
         'compFam_variablesCharacDesign',
         'compFam_qualityApproverId',
         'compFam_technicalReviewerId',
         'compFam_signatureDate',
         'compFam_validate',
         'compFam_version',
         'compFam_active',
         'compFam_genDesign',
         'compFam_genRef'] ;

     //Define the relation between a compFamily and its compSubFamily : a compSubFamily can correspond to only one compFamily
     public function comp_sub_family(){
         return $this->hasMany(CompSubFamily::class) ;
     }

    //Define the relation between a compFamily and the user who approved it : a compFamily has only one qualityApprover
     public function quality_approver(){
        return $this->belongsTo(User::class, 'compFam_qualityApproverId') ;
    }

     //Define the relation between a compFamily and the user who reviewed it : a compFamily has only one technicalReviewer
     public function technical_reviewer(){
        return $this->belongsTo(User::class, 'compFam_technicalReviewerId') ;
    }

     //Define the relation between an EnumStorageCondition and its compFamily : a compFamily can correspond to many EnumStorageCondition
     public function storage_conditions(){
        return $this->belongsToMany(EnumStorageCondition::class, 'pivot_comp_fam_sto_cond', 'compFam_id', 'storageCondition_id') ;
    }

    //Define the relation between an EnumPurchasedBy and its compFamily : a compFamily has only one EnumPurchasedBy
    public function purchased_by(){
        return $this->belongsTo(EnumPurchasedBy::class, 'enumPurchasedBy_id') ;
    }

     //Define the relation between a supplier and its compFamily : a compFamily can correspond to many suppliers
     public function suppliers(){
        return $this->belongsToMany(Supplier::class, 'pivot_comp_fam_supplr', 'compFam_id', 'supplr_id') ;
    }

    //Define the relation between a compFamily and its criticality : a compFamily has only one criticality
    public function criticality(){
        return $this->HasOne(Criticality::class) ;
    }

}
