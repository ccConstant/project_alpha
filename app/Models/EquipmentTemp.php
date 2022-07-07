<?php

/*
* Filename : EquipmentTemp.php
* Creation date : 29 Apr 2022
* Update date : 10 May 2022
* This file define the model EquipmentTemp. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_062219_create_equipment_temps_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Equipment ; 
use App\Models\People; 
use App\Models\SpecialProcess; 
use App\Models\Power;
use App\Models\State;  
use App\Models\PreventiveMaintenanceOperation ; 
use App\Models\Risk ;
use App\Models\Usage ; 
use App\Models\Dimension ;
use App\Models\File ;  
use App\Models\Mme ;  
use App\Models\EnumEquipmentMassUnit ; 
use App\Models\EnumEquipmentType ; 

class EquipmentTemp extends Model{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['eqTemp_version', 'eqTemp_date', 'eqTemp_validate', 'eqTemp_mass', 'eqTemp_remarks', 'enumMassUnit_id', 'equipment_id', 'eqTemp_mobility', 'enumType_id','qualityVerifier_id', 'technicalVerifier_id', 'createdBy_id', 'specialProcess_id', 'eqTemp_lifeSheetCreated'] ; 

    
    //Define the relation between an equipment_temp and its files : an equipment_temp can have many files
     public function files(){
        return $this->hasMany(File::class) ; 
    }

    //Define the relation between an equipment_temp and its mme : an equipment_temp can have many mme
    public function mmes(){
        return $this->hasMany(Mme::class) ; 
    }

    //Define the relation between an equipment_temp and the equipment which he is linked : an equipment_temp can be linked to only one equipment 
    public function equipment(){
        return $this->belongsTo(Equipment::class, 'equipment_id') ; 
    }

     //Define the relation between an equipment temp and the unit of its mass : an equipment has only one unit mass
     public function enumEquipmentMassUnit(){
        return $this->belongsTo(EnumEquipmentMassUnit::class, 'enumMassUnit_id') ; 
    }

    //Define the relation between an equipment temp and its type : an equipment has only one type
      public function enumEquipmentType(){
        return $this->belongsTo(EnumEquipmentType::class, 'enumType_id') ; 
    }

    //Define the relation between an equipment_temp and the person who cheking it : an equipment_temp has only one qualityVerifier
    public function qualityVerifier(){
        return $this->belongsTo(People::class, 'qualityVerifier_id') ; 
    }

    //Define the relation between an equipment_temp and the person who cheking it : an equipment_temp has only one technicalVerifier
    public function technicalVerifier(){
        return $this->belongsTo(People::class, 'technicalVerifier_id') ; 
    }

    //Define the relation between an equipment_temp and the person who creating it : an equipment_temp has only one creator (person who makes the changes creating the equipment_temp)
    public function createdBy(){
        return $this->belongsTo(People::class, 'createdBy_id') ; 
    }

    //Define the relation between an equipment_temp and its special_process : an equipment_temp has only one special_process
    public function special_process(){
        return $this->belongsTo(SpecialProcess::class, 'specialProcess_id') ; 
    }

    //Define the relation between an equipment_temp and its powers : an equipment_temp can have many powers
    public function powers(){
        return $this->hasMany(Power::class);
    }

    //Define the relation between an equipment_temp and its states : an equipment_temp can have many states
    public function states(){
        return $this->belongsToMany(State::class, 'pivot_equipment_temp_state', 'equipmentTemp_id', 'state_id') ; 
    }

    //Define the relation between an equipment_temp and its preventive maintenance operations : an equipment_temp can have many preventive maintenance operations
    public function preventive_maintenance_operations(){
        return $this->hasMany(PreventiveMaintenanceOperation::class);
    }

    //Define the relation between an equipment_temp and its risks : an equipment_temp can have many risks
    public function risks(){
        return $this->hasMany(Risk::class) ; 
    }

    //Define the relation between an equipment_temp and its usages : an equipment_temp can have many usages
     public function usages(){
        return $this->hasMany(Usage::class);
    }
 

    //Define the relation between an equipment_temp and its dimensions : an equipment_temp can have many dimensions
     public function dimensions(){
        return $this->hasMany(Dimension::class) ;
    }

}
