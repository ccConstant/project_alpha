<?php

/*
* Filename : SpecialProcess.php
* Creation date : 9 May 2022
* Update date : 10 May 2022
* This file define the model SpecialProcess. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_062107_create_special_processes_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models\SW01;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW01\EquipmentTemp ; 

class SpecialProcess extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['spProc_exist', 'spProc_remarksOrPrecaution', 'spProc_validate', 'spProc_name'] ; 

    //Define the relation between special_process and equipment_temp : an special_process can match for many equipment_temp
    public function equipment_temps(){
        return $this->hasMany(EquipmentTemps::class) ; 
    }
}
