<?php

/*
* Filename : Power.php
* Creation date : 9 May 2022
* Update date : 9 May 2022
* This file define the model Power. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_063017_create_powers_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EquipmentTemp ; 
use App\Models\EnumPowerType ; 

class Power extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['pow_name', 'pow_value', 'pow_unit', 'pow_consumptionValue', 'pow_consumptionUnit', 'pow_validate', 'enumPowerType_id', 'equipmentTemp_id']  ;


    //Define the relation between a power and his type : a power can have only one type
    public function enumPowerType(){
        return $this->belongsTo(EnumPowerType::class, 'enumPowerType_id') ; 
    }

    //Define the relation between an equipment_temp and its powers : a power can correspond to many equipment temps
    public function equipment_temps(){
        return $this->belongsToMany(EquipmentTemp::class, 'equipmentTemp_id') ; 
    }
}
