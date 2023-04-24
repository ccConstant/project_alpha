<?php

/*
* Filename : CompCriticality.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
* This file define the model CompCriticality. We can see more details about this model (like his attributes) in the
* migration file named "2023_04_20_091843_create_comp_criticalities_table.php" in Database>migrations."
*
*/

namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompCriticality extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['compCrit_compCriticality, compCrit_compMaterialContactCriticality', 'compCrit_compMaterialFunctionCriticality', 'compCrit_compProcessCriticality', 'compCrit_qualityApproverId', 'compCrit_technicalReviewerId', 'compCrit_signatureDate', 'compCrit_validate', 'compCrit_remarks', 'compFam_id'] ;

    //Define the relation between a compCriticality and the comp_family which he is linked : a compCriticality can be linked to only one comp_family
    public function comp_family(){
        return $this->belongsTo(CompFamily::class, 'compFam_id') ;
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
