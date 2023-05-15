<?php

/*
* Filename : ConsFamilyMember.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
* This file define the model ConsFamilyMember. We can see more details about this model (like his attributes) in the
* migration file named "2023_04_20_080526_create_cons_family_members_table.php" in Database>migrations."
*
*/

namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\ConsFamily;
use App\Models\User;

class ConsFamilyMember extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['consMb_dimension', 'consMb_technicalReviewerId', 'consMb_qualityApproverId', 'consMb_signatureDate', 'consFam_id', 'consMb_design', 'consMb_sameValues'] ;

    //Define the relation between a cons_family_member and the cons_family which he is linked : a cons_family_member can be linked to only one cons_family
    public function cons_family(){
        return $this->belongsTo(ConsFamily::class, 'consFam_id') ;
    }

    //Define the relation between a consFamilyMember and the user who approved it : a consFamilyMember has only one qualityApprover
    public function quality_approver(){
        return $this->belongsTo(User::class, 'consMb_qualityApproverId') ;
    }

     //Define the relation between a consFamilyMember and the user who reviewed it : a consFamilyMember has only one technicalReviewer
     public function technical_reviewer(){
        return $this->belongsTo(User::class, 'consMb_technicalReviewerId') ;
    }

}
