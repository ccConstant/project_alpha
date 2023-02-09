<?php

/*
* Filename : Precaution.php
* Creation date : 9 Jun 2022
* Update date : 8 Feb 2023
* This file define the model Precaution. We can see more details about this model (like his attributes) in the 
* migration file named "2022_06_09_073838_create_precautions_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EnumPrecautionType ;
use App\Models\MmeUsage ;

class Precaution extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['prctn_description', 'prctn_validate', 'enumPrecautionType_id', 'mmeUsage_id'];
    
    //Define the relation between a precaution and the usage in which it is linked
    public function usage(){
        return $this->belongsTo(MmeUsage::class, 'mmeUsage_id') ; 
    }

    //Define the relation between a precaution a its type : a precaution has only one type
    public function enumPrecautionType(){
        return $this->belongsTo(EnumPrecautionType::class, 'enumPrecautionType_id') ; 
    }
}
