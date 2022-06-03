<?php

/*
* Filename : State.php
* Creation date : 9 May 2022
* Update date : 10 May 2022
* This file define the model State. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_061849_create_states_table.php" in Database>migrations." 
* 
*/ 


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Equipment ; 
use App\Models\CurativeMaintenanceOperation ; 
use App\Models\PreventiveMaintenanceOperationRealized ;
use App\Models\People ; 
use App\Models\EquipmentTemp ;  

class State extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['state_remarks', 'state_startDate', 'state_endDate', 'state_isOk', 'state_validate', 'reformedBy_id', 'state_name'] ; 
    
    //Define the relation between equipment and state : only one equipment can be created during a state
    public function equipment(){
        return $this->hasOne(Equipment::class) ; 
    }

    //Define the relation between a state and the curative_maintenance_operations that can take place during the state
    public function curative_maintenance_operations(){
        return $this->hasMany(CurativeMaintenanceOperation::class) ; 
    }

    //Define the relation between a state and the preventive_maintenance_operation_realizeds that can take place during the state
    public function preventive_maintenance_operation_realizeds(){
        return $this->hasMany(PreventiveMaintenanceOperationRealized::class) ; 
    }

    //Define the relation between a state and the person who reformed the equipment during the state 
    public function reformedBy(){
        return $this->hasOne(People::class, 'reformedBy_id') ; 
    }

    //Define the relation between an equipment_temp and its state : a state can correspond to many equipment temps
    public function equipment_temps(){
        return $this->belongsToMany(EquipmentTemp::class, 'pivot_equipment_temp_state', 'state_id', 'equipmentTemp_id') ; 
    }


}

