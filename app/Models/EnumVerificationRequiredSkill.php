<?php

/*
* Filename : EnumVerificationRequiredSkill.php
* Creation date : 7 Jun 2022
* Update date : 7 Jun 2022
* This file define the model EnumVerificationRequiredSkill. We can see more details about this model (like his attributes) in the 
* migration file named "2022_06_07_164554_create_enum_verification_required_skills_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnumVerificationRequiredSkill extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['value'] ; 

     //Define the relation between a verification and his verif required skill : a required skill can correspond to many verifications
     public function verifications(){
        return $this->hasMany(Verification::class) ; 
    }
}
