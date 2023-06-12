<?php

/*
* Filename : EquipmentTemp.php
* Creation date : 10 May 2022
* Update date : 31 Jan 2023
* This file define the model EquipmentTemp. We can see more details about this model (like his attributes) in the
* migration file named "2022_05_10_062219_create_equipment_temps_table.php" in Database>migrations."
*
*/

namespace App\Models\SW01;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW01\Equipment ;
use App\Models\User;
use App\Models\SW01\SpecialProcess;
use App\Models\SW01\Power;
use App\Models\SW01\State;
use App\Models\SW01\PreventiveMaintenanceOperation ;
use App\Models\SW01\Risk ;
use App\Models\SW01\Usage ;
use App\Models\SW01\Dimension ;
use App\Models\File ;
use App\Models\SW01\Mme ;
use App\Models\SW01\EnumEquipmentMassUnit ;
use App\Models\SW01\EnumEquipmentType ;
use App\Models\History ;

class EquipmentTemp extends Model{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = [
        'eqTemp_version',
        'eqTemp_date',
        'eqTemp_validate',
        'eqTemp_location',
        'eqTemp_mass',
        'eqTemp_remarks',
        'enumMassUnit_id',
        'equipment_id',
        'eqTemp_mobility',
        'enumType_id',
        'qualityVerifier_id',
        'technicalVerifier_id',
        'createdBy_id',
        'specialProcess_id',
        'eqTemp_lifeSheetCreated',
        'eqTemp_signatureDate',
    ] ;


    //Define the relation between an equipment_temp and its files : an equipment_temp can have many files
     public function files(){
        return $this->hasMany(File::class) ;
    }

    //Define the relation between an equipment_temp and its histories : an equipment_temp can have many histories
    public function histories(){
        return $this->hasMany(History::class) ;
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
        return $this->belongsTo(User::class, 'qualityVerifier_id') ;
    }

    //Define the relation between an equipment_temp and the person who cheking it : an equipment_temp has only one technicalVerifier
    public function technicalVerifier(){
        return $this->belongsTo(User::class, 'technicalVerifier_id') ;
    }

    //Define the relation between an equipment_temp and the person who creating it : an equipment_temp has only one creator (person who makes the changes creating the equipment_temp)
    public function createdBy(){
        return $this->belongsTo(User::class, 'createdBy_id') ;
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
