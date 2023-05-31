<?php

/*
* Filename : CompFamilyMember.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
* This file define the model CompFamilyMember. We can see more details about this model (like his attributes) in the
* migration file named "2023_05_31_122307_create_comp_family_members_table.php" in Database>migrations."
*/

namespace App\Models\SW03;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\CompFamily;

class CompFamilyMember extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = [
        'compMb_dimension',
        'compMb_technicalReviewerId',
        'compMb_qualityApproverId',
        'compMb_signatureDate',
        'compSubFam_id',
        'compMb_design',
        'compMb_sameValues'
    ];

    /*Define the relation between a comp_family_member and the comp_sub_family which he is linked:
    a comp_family_member can be linked to only one comp_sub_family*/
    public function comp_sub_family(){
        return $this->belongsTo(CompSubFamily::class, 'compSubFam_id') ;
    }

    //Define the relation between a compFamilyMember and the user who approved it : a compFamilyMember has only one qualityApprover
    public function quality_approver(){
        return $this->belongsTo(User::class, 'consMb_qualityApproverId') ;
    }

     //Define the relation between a compFamilyMember and the user who reviewed it : a compFamilyMember has only one technicalReviewer
     public function technical_reviewer(){
        return $this->belongsTo(User::class, 'consMb_technicalReviewerId') ;
    }
}
