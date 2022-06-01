<?php

/*
* Filename : PreventiveMaintenanceOperation.php
* Creation date : 10 May 2022
* Update date : 10 May 2022
* This file define the model PreventiveMaintenanceOperation. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_062625_create_preventive_maintenance_operations_table.php" in Database>migrations." 
* 
*/ 


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EquipmentTemp ; 
use App\Models\PreventiveMaintenanceOperationRealized ; 
use App\Models\Risk ; 

class PreventiveMaintenanceOperation extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['prvMtnOp_number', 'prvMtnOp_description','prvMtnOp_periodicity','prvMtnOp_symbolPeriodicity', 'prvMtnOp_protocol', 'prvMtnOp_startDate', 'prvMtnOp_nextDate', 'prvMtnOp_reformDate', 'prvMtnOp_validate', 'equipmentTemp_id' ];

     //Define the relation between a preventive maintenance operation and its risks : a preventive maintenance operations has many risks
     public function risks(){
        return $this->hasMany(Risk::class) ; 
    }

    //Define the relation between a preventive maintenance operation and a preventive maintenance operation realized :  like a preventive maintenance operative can be realized many times, a preventive maintenance operative has many preventive maintenance operation realized
    public function preventiveMaintenanceOperationRealizeds(){
        return $this->hasMany(PreventiveMaintenanceOperationRealized::class) ; 
    }

    //Define the relation between an equipment_temp and its preventive maintenance operations : a preventive maintenance operations can correspond to many equipments temps
    public function equipment_temps(){
        return $this->belongsTo(EquipmentTemp::class, 'equipmentTemp_id') ; 
    }

}

