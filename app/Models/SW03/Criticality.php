<?php

/*
* Filename : Criticality.php
* Creation date : 20 Apr 2023
* Update date : 10 Jul 2023
* This file define the model Criticality. We can see more details about this model (like his attributes) in the
* migration file named "2023_04_20_091843_create_criticalities_table.php" in Database>migrations."
*
*/

namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\RawFamily;
use App\Models\User;

class Criticality extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['crit_performanceMedicalDevice', 'crit_checkedTests', 'crit_checkedTestRadioFunc','crit_checkedTestRadioAsp' ,'crit_checkedTestRadioDoc','crit_checkedTestRadioAdm','crit_artCriticality', 'crit_qualityApproverId', 'crit_technicalReviewerId', 'crit_signatureDate', 'crit_validate', 'crit_remarks', 'compFam_id', 'consFam_id', 'rawFam_id'] ;

    //Define the relation between a compCriticality and the comp_family which he is linked : a compCriticality can be linked to only one comp_family
    public function comp_family(){
        return $this->belongsTo(CompFamily::class, 'compFam_id') ;
    }

    //Define the relation between a consCriticality and the cons_family which he is linked : a consCriticality can be linked to only one cons_family
    public function cons_family(){
        return $this->belongsTo(ConsFamily::class, 'consFam_id') ;
    }

    //Define the relation between a rawCriticality and the raw_family which he is linked : a rawCriticality can be linked to only one raw_family
    public function raw_family(){
        return $this->belongsTo(RawFamily::class, 'rawFam_id') ;
    }

    //Define the relation between a compCriticality and the user who approved it : a compCriticality has only one qualityApprover
    public function quality_approver(){
        return $this->belongsTo(User::class, 'compCrit_qualityApproverId') ;
    }

    //Define the relation between a compCriticality and the user who reviewed it : a compCriticality has only one technicalReviewer
    public function technical_reviewer(){
        return $this->belongsTo(User::class, 'compCrit_technicalReviewerId') ;
    }
}
