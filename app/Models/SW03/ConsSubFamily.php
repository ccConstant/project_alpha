<?php

/*
* Filename : ConsSubFamily.php
* Creation date : 3 Jul 2023
* Update date : 4 Jul 2023
* This file define the model ConsSubFamily. We can see more details about this model (like his attributes) in the
* migration file named " 2023_04_20_075945_create_cons_sub_families_table.php" in Database>migrations."
*
*/

namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\ConsFamilyMember;
use App\Models\User;
use App\Models\SW03\EnumPurchasedBy;
use App\Models\SW03\ConsFamily;

class ConsSubFamily extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['consSubFam_ref',
    'consSubFam_design',
    'consSubFam_drawingPath',
    'enumPurchasedBy_id',
    'consSubFam_nbrVersion',
    'consSubFam_qualityApproverId',
    'consSubFam_technicalReviewerId',
    'consSubFam_signatureDate',
    'consSubFam_validate',
    'consSubFam_version',
    'consSubFam_active',
    'consFam_id'] ;
   

    //Define the relation between a consFamily and its consSubFamily : a consSubFamily can correspond to only one consFamily
    public function cons_family_member(){
        return $this->hasMany(ConsFamilyMember::class) ;
    }

    //Define the relation between a cons_sub_family and the cons_family which he is linked : a cons_sub_family can be linked to only one cons_family
    public function cons_family(){
        return $this->belongsTo(ConsFamily::class, 'consFam_id') ;
    }

    //Define the relation between a consSubFamily and the user who approved it : a consSubFamily has only one qualityApprover
    public function quality_approver(){
    return $this->belongsTo(User::class, 'consSubFam_qualityApproverId') ;
    }

    //Define the relation between a consSubFamily and the user who reviewed it : a consSubFamily has only one technicalReviewer
    public function technical_reviewer(){
    return $this->belongsTo(User::class, 'compSubFam_technicalReviewerId') ;
    }

    //Define the relation between an EnumPurchasedBy and its consSubFamily : a consSubFamily has only one EnumPurchasedBy
    public function purchased_by(){
    return $this->belongsTo(EnumPurchasedBy::class, 'enumPurchasedBy_id') ;
    }
}



