<?php

/*
* Filename : PurchaseSpecification.php
* Creation date : 20 Apr 2023
* Update date : 10 Jul 2023
* This file define the model PurchaseSpecification. We can see more details about this model (like his attributes) in the
* migration file named "2023_04_20_090129_create_purchase_specifications_table.php"
*
*/

namespace App\Models\SW03;

use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\RawFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\CompFamily;

class PurchaseSpecification extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['purSpe_qualityApproverId', 'purSpe_technicalReviewerId', 'purSpe_signatureDate', 'purSpe_validate', 'rawFam_id', 'consFam_id', 'compFam_id', 'rawSubFam_id', 'consSubFam_id', 'compSubFam_id'] ;

    //Define the relation between a purchaseSpecification and the raw_family which he is linked : a purchaseSpecification can be linked to only one raw_family
    public function raw_family(){
        return $this->belongsTo(RawFamily::class, 'rawFam_id') ;
    }

    //Define the relation between a purchaseSpecification and the comp_family which he is linked : a purchaseSpecification can be linked to only one comp_family
    public function comp_family(){
        return $this->belongsTo(CompFamily::class, 'compFam_id') ;
    }

    //Define the relation between a purchaseSpecification and the cons_family which he is linked : a purchaseSpecification can be linked to only one cons_family
    public function cons_family(){
        return $this->belongsTo(ConsFamily::class, 'consFam_id') ;
    }

    //Define the relation between a purchaseSpecification and the user who approved it : a purchaseSpecification has only one qualityApprover
    public function quality_approver(){
        return $this->belongsTo(User::class, 'purSpe_qualityApproverId') ;
    }

    //Define the relation between a purchaseSpecification and the user who reviewed it : a purchaseSpecification has only one technicalReviewer
    public function technical_reviewer(){
        return $this->belongsTo(User::class, 'purSpe_technicalReviewerId') ;
    }

    //Define the relation between a purchaseSpecification and his files : a purchaseSpecification has many files
    public function files(){
        return $this->hasMany(File::class, 'purSpe_id') ;
    }
}
