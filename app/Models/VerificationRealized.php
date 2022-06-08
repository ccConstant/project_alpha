<?php

/*
* Filename : VerificationRealized.php
* Creation date : 8 Jun 2022
* Update date : 8 Jun 2022
* This file define the model VerificationRealized. We can see more details about this model (like his attributes) in the 
* migration file named "2022_06_08_064359_create_verification_realizeds_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationRealized extends Model
{
    use HasFactory;

     //Data which can be added, updated or deleted by us in the data base.
     protected $fillable = ['verifRlz_reportNumber', 'verifRlz_startDate', 'verifRlz_endDate', 'verifRlz_nextDate', 'verifRlz_entryDate', 'verifRlz_validate', 'verifRlz_isPassed', 'enteredBy_id', 'realizedBy_id', 'state_id', 'verif_id'];
    //Define the relation between an preventive_maintenance_operation_realized and the state during wich she is realized
    public function state(){
        return $this->belongsTo(MmeState::class, 'state_id') ; 
    }

     //Define the relation between a verification realized and the person realizing it 
     public function realizedBy(){
        return $this->belongsTo(People::class, 'realizedBy_id') ; 
    }

    //Define the relation between a verification realized and the person entering it 
    public function enteredBy(){
        return $this->belongsTo(People::class, 'enteredBy_id') ; 
    }

    //Define the relation between a verification realized and the verification which she is corresponding
    public function verification(){
        return $this->belongsTo(verification::class, 'verif_id') ; 
    }
}
