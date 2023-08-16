<?php

/*
* Filename : RawSubFamily.php
* Creation date : 3 Jul 2023
* Update date : 4 Jul 2023
* This file define the model RawSubFamily. We can see more details about this model (like his attributes) in the
* migration file named " 2023_04_20_083400_create_raw_sub_families_table.php" in Database>migrations."
*
*/

namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\RawFamilyMember;
use App\Models\User;
use App\Models\SW03\EnumPurchasedBy;
use App\Models\SW03\EnumStorageCondition;
use App\Models\SW03\RawFamily;

class RawSubFamily extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['rawSubFam_ref',
    'rawSubFam_design',
    'rawSubFam_drawingPath',
    'enumPurchasedBy_id',
    'rawSubFam_nbrVersion',
    'rawSubFam_qualityApproverId',
    'rawSubFam_technicalReviewerId',
    'rawSubFam_signatureDate',
    'rawSubFam_validate',
    'rawSubFam_active',
    'rawSubFam_materials',
    'rawSubFam_version',
    'rawFam_id'] ;
   

    //Define the relation between a rawFamily and its rawSubFamily : a rawSubFamily can correspond to only one rawFamily
    public function raw_family_member(){
        return $this->hasMany(RawFamilyMember::class) ;
    }

    //Define the relation between a raw_sub_family and the raw_family which he is linked : a raw_sub_family can be linked to only one raw_family
    public function raw_family(){
        return $this->belongsTo(RawFamily::class, 'rawFam_id') ;
    }

    //Define the relation between a rawSubFamily and the user who approved it : a rawSubFamily has only one qualityApprover
    public function quality_approver(){
        return $this->belongsTo(User::class, 'rawSubFam_qualityApproverId') ;
    }

    //Define the relation between a rawSubFamily and the user who reviewed it : a rawSubFamily has only one technicalReviewer
    public function technical_reviewer(){
        return $this->belongsTo(User::class, 'rawSubFam_technicalReviewerId') ;
    }

    //Define the relation between an EnumPurchasedBy and its rawSubFamily : a rawSubFamily has only one EnumPurchasedBy
    public function purchased_by(){
        return $this->belongsTo(EnumPurchasedBy::class, 'enumPurchasedBy_id') ;
    }

     //Define the relation between an EnumStorageCondition and its rawSubFamily : a rawSubFamily can correspond to many EnumStorageCondition
     public function storage_conditions(){
        return $this->belongsToMany(EnumStorageCondition::class, 'pivot_raw_sub_fam_sto_cond', 'rawSubFam_id', 'storageCondition_id') ;
    }

    //Define the relation between a supplier and its rawSubFamily : a rawSubFamily can correspond to many suppliers
      public function suppliers(){
        return $this->belongsToMany(Supplier::class, 'pivot_raw_sub_fam_supplr', 'rawSubFam_id', 'supplr_id') ;
    }
}