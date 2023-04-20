<?php

/*
* Filename : EnumStorageCondition.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
* This file define the model EnumStorageCondition. We can see more details about this model (like his attributes) in the 
* migration file named "2023_04_20_093809_create_enum_storage_conditions_table.php" 
* 
*/ 

namespace App\Models\SW02;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW02\CompFamily;

class EnumStorageCondition extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['value'] ;

    //Define the relation between an EnumStorageCondition and its compFamily : an EnumStorageCondition can have many compFamily
    public function compFamily(){
        return $this->belongsToMany(CompFamily::class, 'pivot_comp_fam_sto_cond', 'storageCondition_id', 'compFam_id') ; 
    }
}
