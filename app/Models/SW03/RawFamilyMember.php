<?php

/*
* Filename : RawFamilyMember.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
* This file define the model RawFamilyMember. We can see more details about this model (like his attributes) in the
* migration file named "2023_05_31_123900_create_raw_family_members_table.php" in Database>migrations."
*/

namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\RawFamily;
use App\Models\User;

class RawFamilyMember extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = [
        'rawMb_dimension',
        'rawMb_technicalReviewerId',
        'rawMb_qualityApproverId',
        'rawMb_signatureDate',
        'rawSubFam_id',
        'rawMb_design',
        'rawMb_sameValues'
    ] ;

    /*Define the relation between a raw_family_member and the raw_sub_family which he is linked:
    a raw_family_member can be linked to only one raw_sub_family*/
    public function raw_sub_family(){
        return $this->belongsTo(RawSubFamily::class, 'rawSubFam_id') ;
    }

    //Define the relation between a rawFamilyMember and the user who approved it : a rawFamilyMember has only one qualityApprover
    public function quality_approver(){
        return $this->belongsTo(User::class, 'rawMb_qualityApproverId') ;
    }

     //Define the relation between a rawFamilyMember and the user who reviewed it : a rawFamilyMember has only one technicalReviewer
     public function technical_reviewer(){
        return $this->belongsTo(User::class, 'rawMb_technicalReviewerId') ;
    }
}
