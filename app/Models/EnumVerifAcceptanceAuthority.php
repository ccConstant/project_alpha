<?php

/*
* Filename : EnumUsageVerifAcceptanceAuthority.php
* Creation date : 8 Jun 2022
* Update date : 31 Jan 2023
* This file define the model EnumUsageVerifAcceptanceAuthority. We can see more details about this model (like his attributes) in the 
* migration file named "2022_06_08_120123_create_enum_usage_verif_acceptance_authorities_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MmeUsage ; 


class EnumVerifAcceptanceAuthority extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['value'] ; 

     //Define the relation between a usage and his verif acceptance authoritity : a verif acceptance authority can correspond to many mme_usages
     public function mme_usages(){
        return $this->hasMany(MmeUsage::class) ; 
    }
}
