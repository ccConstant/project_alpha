<?php

/*
* Filename : People.php
* Creation date : 9 May 2022
* Update date : 9 May 2022
* This file define the model People. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_09_083158_create_peoples_table.php" in Database>migrations." 
* 
*/ 


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PreventiveMaintenanceOperation ; 
use App\Models\CurativeMaintenanceOperation ; 
use App\Models\EquipmentTemp ; 
use App\Models\State ; 

class People extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['people_firstName', 'people_lastName', 'people_initials', 'people_signaturePath', 'people_validate', 'people_rightLevel', 'people_userName', 'people_password', 'people_startDate', 'people_endDate'];  

    //Define the relation between a preventive_maintenance_operation and the person who realizing it : a person can realize many preventive maintenance operations
    public function prvMtnOp_realizedBy(){
        return $this->hasMany(PreventiveMaintenanceOperation::class) ;
    }

    //Define the relation between a preventive_maintenance_operation and the person who entering it : a person can enter many preventive maintenance operations
    public function prvMtnOp_enteredBy(){
        return $this->hasMany(PreventiveMaintenanceOperation::class) ;
    }

    //Define the relation between a curative_maitenance_operation and the person who entering it : a person can enter many curative maintenance operations 
    public function curMtnOp_enteredBy(){
        return $this->hasMany(CurativeMaintenanceOperation::class) ;
    }

    //Define the relation between a curative_maintenance_operation and the person who realizing it : a person can realize many curative maintenance operations
     public function curMtnOp_realizedBy(){
        return $this->hasMany(CurativeMaintenanceOperation::class) ;
    }

    //Define the relation between a curative_maintenance_operation and the person who checking it : a qualityVerifier can check many curative maintenance operations
    public function curMtnOp_qualityVerifier(){
        return $this->hasMany(CurativeMaintenanceOperation::class) ;
    }

    //Define the relation between a curative_maintenance_operation and the person who checking it : a technicalVerifier can check many curative maintenance operations
    public function curMtnOp_technicalVerifier(){
        return $this->hasMany(CurativeMaintenanceOperation::class) ;
    }

    //Define the relation between an equipment_temp and the person who checking it : a technicalVerifier can check many equipment temps
    public function equipmentTemp_technicalVerifier(){
        return $this->hasMany(EquipmentTemp::class) ;
    }

    //Define the relation between an equipment_temp and the person who checking it : a qualityVerifier can check many equipment temps
    public function equipmentTemp_qualityVerifier(){
        return $this->hasMany(EquipmentTemp::class) ;
    }

    //Define the relation between an equipment_temp and the person who creating it : a person can create many equipment temps
    public function equipmentTemp_createdBy(){
        return $this->belongsTo(EquipmentTemp::class) ;
    }

    //Define the relation between a state and the person who reformed an equipement during the state : a person can reformed many equipments
    public function state_reformedBy(){
        return $this->hasMany(State::class) ;
    }

    



}
