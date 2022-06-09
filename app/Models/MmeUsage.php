<?php

/*
* Filename : MmeUsage.php
* Creation date : 9 Jun 2022
* Update date : 9 Jun 2022
* This file define the model Usage. We can see more details about this model (like his attributes) in the 
* migration file named "2022_06_09_072655_create_mme_usages_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MmeUsage extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['usg_mesurementType','usg_precision','usg_application','usg_startDate','usg_reformDate','usg_validate', 'enumUsageMetrologicalLevel_id','enumUsageVerifAcceptanceAuthority_id','mmeTemp_id'] ;

    //Define the relation between an mme_temp and its usages : a usage can correspond to many mme temps
   public function mme_temps(){
       return $this->belongsTo(MmeTemp::class, 'mmeTemp_id') ; 
   }

     //Define the relation between a usage and its precaution : a usage can have many precaution
     public function precaution(){
        return $this->hasMany(Precaution::class) ; 
    }
}
