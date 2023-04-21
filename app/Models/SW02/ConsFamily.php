<?php

/*
* Filename : ConsFamily.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
* This file define the model ConsFamily. We can see more details about this model (like his attributes) in the 
* migration file named "2023_04_20_075848_cons_families.php" in Database>migrations." 
* 
*/ 

namespace App\Models\SW02;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW02\ConsFamilyMember;
use App\Models\User;
use App\Models\SW02\EnumStorageCondition;
use App\Models\SW02\PurchaseSpecification;
use App\Models\SW02\IncomingInspection;
use App\Models\SW02\Supplier;

class ConsFamily extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['consFam_ref', 'consFam_design', 'consFam_drawingPath', 'consFam_purchasedBy', 'consFam_nbrVersion', 'consFam_variablesCharac', 'consFam_qualityApproverId', 'consFam_technicalReviewerId', 'consFam_signatureDate', 'consFam_validate', 'consFam_version', 'consFam_active'] ;

    //Define the relation between a consFamily and its consFamilyMember : a consFamilyMember can correspond to only one consFamily
    public function cons_family_member(){
        return $this->hasMany(ConsFamilyMember::class) ; 
    }

    //Define the relation between a consFamily and the user who approved it : a consFamily has only one qualityApprover
    public function quality_approver(){
        return $this->belongsTo(User::class, 'consFam_qualityApproverId') ; 
    }

     //Define the relation between a consFamily and the user who reviewed it : a consFamily has only one technicalReviewer
     public function technical_reviewer(){
        return $this->belongsTo(User::class, 'consFam_technicalReviewerId') ; 
    }

     //Define the relation between an EnumStorageCondition and its consFamily : a consFamily can correspond to many EnumStorageCondition
     public function storage_condition(){
        return $this->belongsToMany(EnumStorageCondition::class, 'pivot_cons_fam_sto_cond', 'consFam_id', 'storageCondition_id') ; 
    }

    //Define the relation between a consFamily and its purchaseSpecifications : a purchaseSpecifications can correspond to only one consFamily
    public function purchase_specifications(){
        return $this->hasMany(PurchaseSpecification::class) ; 
    }

    //Define the relation between a consFamily and its purchaseSpecifications : a purchaseSpecifications can correspond to only one consFamily
    public function incoming_inspection(){
        return $this->hasMany(IncomingInspection::class) ; 
    }

     //Define the relation between a supplier and its consFamily : a consFamily can correspond to many suppliers
     public function suppliers(){
        return $this->belongsToMany(Supplier::class, 'pivot_cons_fam_supplr', 'consFam_id', 'supplr_id') ; 
    }

    




}
