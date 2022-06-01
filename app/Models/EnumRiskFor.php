<?php

/*
* Filename : EnumRiskFor.php
* Creation date : 9 May 2022
* Update date : 9 May 2022
* This file define the model EnumRiskFor. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_063721_create_enum_risk_fors_table.php" in Database>migrations." 
* 
*/ 


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Risk ; 

class EnumRiskFor extends Model
{
    use HasFactory;

     //Data which can be added, updated or deleted by us in the data base.
     protected $fillable = ['value'] ; 

     //Define the relation between a risk and his target : a target can correspond to many risks
    public function risks(){
        return $this->hasMany(Risk::class) ; 
    }

}

