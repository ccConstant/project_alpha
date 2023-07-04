<?php

/*
* Filename : CompSubFamily.php
* Creation date : 3 Jul 2023
* Update date : 4 Jul 2023
* This file define the model CompSubFamily. We can see more details about this model (like his attributes) in the
* migration file named " 2023_06_28_142531_create_comp_sub_families_table.php" in Database>migrations."
*
*/

namespace App\Models\SW03;


use App\Models\SW03\CompFamilyMember;
use App\Models\User;
use App\Models\SW03\EnumPurchasedBy;
use App\Models\SW03\CompFamily;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CompSubFamily extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['compSubFam_ref',
    'compSubFam_design',
    'compSubFam_drawingPath',
    'enumPurchasedBy_id',
    'compSubFam_nbrVersion',
    'compSubFam_qualityApproverId',
    'compSubFam_technicalReviewerId',
    'compSubFam_signatureDate',
    'compSubFam_validate',
    'compSubFam_version',
    'compSubFam_active',
    'compFam_id'] ;

    //Define the relation between a compFamily and its compSubFamily : a compSubFamily can correspond to only one compFamily
    public function comp_family_member(){
        return $this->hasMany(CompFamilyMember::class) ;
    }

    //Define the relation between a comp_sub_family and the comp_family which he is linked : a comp_sub_family can be linked to only one comp_family
    public function comp_family(){
        return $this->belongsTo(CompFamily::class, 'compFam_id') ;
    }

    //Define the relation between a compSubFamily and the user who approved it : a compSubFamily has only one qualityApprover
    public function quality_approver(){
    return $this->belongsTo(User::class, 'compSubFam_qualityApproverId') ;
    }

    //Define the relation between a compSubFamily and the user who reviewed it : a compSubFamily has only one technicalReviewer
    public function technical_reviewer(){
    return $this->belongsTo(User::class, 'compSubFam_technicalReviewerId') ;
    }

    //Define the relation between an EnumPurchasedBy and its compSubFamily : a compFamily has only one EnumPurchasedBy
    public function purchased_by(){
    return $this->belongsTo(EnumPurchasedBy::class, 'enumPurchasedBy_id') ;
    }

}
