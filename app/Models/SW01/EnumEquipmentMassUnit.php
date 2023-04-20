<?php

/*
* Filename : EnumEquipmentMassUnit.php
* Creation date : 5 May 2022
* Update date : 9 May 2022
* This file define the model EnumEquipmentMassUnit. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_09_083035_create_enum_equipment_mass_units_table.php" in Database>migrations.
* 
*/ 


namespace App\Models\SW01;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW01\EquipmentTemp ; 

class EnumEquipmentMassUnit extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['value'] ; 

    //Define the relation between an equipment and his mass unit : a mass unit can correspond to many equipments temps
    public function equipments(){
        return $this->hasMany(EquipmentTemp::class) ; 
    }
}
