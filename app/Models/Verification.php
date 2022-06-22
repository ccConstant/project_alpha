<?php

/*
* Filename : Verification.php
* Creation date : 8 Jun 2022
* Update date : 8 Jun 2022
* This file define the model Verification. We can see more details about this model (like his attributes) in the 
* migration file named "2022_06_08_062325_create_verifications_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EnumVerificationRequiredSkill;

class Verification extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['verif_number','verif_name', 'verif_expectedResult', 'verif_nonComplianceLimit', 'verif_periodicity','verif_symbolPeriodicity', 'enumRequiredSkill_id', 'verif_description', 'verif_protocol', 'verif_startDate', 'verif_nextDate', 'verif_reformDate', 'verif_validate', 'mmeTemp_id' ];


    //Define the relation between a preventive maintenance operation and a preventive maintenance operation realized :  like a preventive maintenance operative can be realized many times, a preventive maintenance operative has many preventive maintenance operation realized
    public function verificationRealizeds(){
        return $this->hasMany(VerificationRealized::class) ; 
    }

    //Define the relation between an equipment_temp and its preventive maintenance operations : a preventive maintenance operations can correspond to many equipments temps
    public function mme_temps(){
        return $this->belongsTo(MmeTemp::class, 'mmeTemp_id') ; 
    }


    //Define the relation between a verification and its required skill : an verification has only one required skill
    public function enumVerificationRequiredSkill(){
        return $this->belongsTo(EnumVerificationRequiredSkill::class, 'verif_requiredSkill') ; 
    }
}
