<?php

/*
* Filename : Equipment.php
* Creation date : 10 May 2022
* Update date : 31 Jan 2023
* This file define the model Equipment. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_062010_create_equipment_table.php" in Database>migrations." 
* 
*/ 


namespace App\Models\SW01;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SW01\EquipmentTemp ; 
use App\Models\SW01\State ; 


class Equipment extends Model{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['eq_internalReference', 'eq_externalReference', 'eq_name', 'eq_serialNumber', 'eq_constructor', 'eq_set', 'eq_nbrVersion', 'state_id'] ; 
    
    //Define the relation between an equipment and its equipmentTemps : an equipment has many equipment_temps
    public function equipment_temps(){
        return $this->hasMany(EquipmentTemp::class) ; 
    }

    //Define the relation between an equipment and the state from which it originates (if it exist) 
    public function state(){
        return $this->belongsTo(State::class, 'state_id') ; 
    }

    

}


