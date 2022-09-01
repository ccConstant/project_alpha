<?php

/*
* Filename : Mme.php
* Creation date : 7 Jun 2022
* Update date : 7 Jun 2022
* This file define the model Mme. We can see more details about this model (like his attributes) in the 
* migration file named "2022_06_07_142255_create_mme_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MmeState;

class Mme extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['mme_internalReference', 'mme_externalReference', 'mme_name', 'mme_serialNumber', 'mme_constructor', 'mme_set', 'mme_nbrVersion', 'state_id', 'equipmentTemp_id'] ; 
    
    //Define the relation between a mme and its mmeTemps: a mme has many mme_temps
    public function mme_temps(){
        return $this->hasMany(MmeTemp::class) ; 
    }

    //Define the relation between an mme and the state from which it originates (if it exist) 
    public function mme_state(){
        return $this->belongsTo(MmeState::class, 'state_id') ; 
    }

     //Define the relation between an equipment_temp and its mmes : a mme can correspond to only one mme
     public function equipment_temps(){
        return $this->belongsTo(EquipmentTemp::class, 'equipmentTemp_id') ; 
    }

    
}
