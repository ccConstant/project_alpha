<?php

/*
* Filename : EnumEquipmentType.php
* Creation date : 5 May 2022
* Update date : 9 May 2022
* This file define the model EnumEquipmentType. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_09_082957_create_enum_equipment_types_table.php" in Database>migrations."
* 
*/ 

namespace App\Models\SW01;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW01\EquipmentTemp ; 

class EnumEquipmentType extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['value'] ; 

    //Define the relation between an equipment temp and his type : a type can correspond to many equipments
    public function equipments(){
        return $this->hasMany(EquipmentTemp::class) ; 
    }
}
