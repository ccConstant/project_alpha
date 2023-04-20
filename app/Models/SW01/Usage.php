<?php

/*
* Filename : Usage.php
* Creation date : 9 May 2022
* Update date : 8 Feb 2023
* This file define the model Usage. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_063905_create_usages_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models\SW01;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW01\EquipmentTemp ; 

class Usage extends Model
{
    use HasFactory;

     //Data which can be added, updated or deleted by us in the data base.
     protected $fillable = ['usg_type', 'usg_precaution', 'usg_startDate', 'usg_reformDate', 'usg_validate', 'equipmentTemp_id'] ; 

     //Define the relation between an equipment_temp and its usages : a usage can correspond to only one equipment temps
    public function equipment_temps(){
        return $this->belongsTo(EquipmentTemp::class, 'equipmentTemp_id') ; 
    }
}
