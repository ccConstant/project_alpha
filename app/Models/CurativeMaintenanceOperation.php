<?php

/*
* Filename : CurativeMaintenanceOperation.php
* Creation date : 10 May 2022
* Update date : 30 Jan 2023
* This file define the model CurativeMaintenanceOperation. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_062350_create_curative_maintenance_operations_table.php" in Database>migrations." 
* 
*/ 


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\State ; 
use App\Models\User ; 

class CurativeMaintenanceOperation extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['curMtnOp_number', 'curMtnOp_reportNumber', 'curMtnOp_description', 'curMtnOp_startDate', 'curMtnOp_endDate', 'curMtnOp_validate', 'state_id', 'mme_state_id','qualityVerifier_id', 'technicalVerifier_id', 'realizedBy_id', 'enteredBy_id' ];

    //Define the relation between a state and the curative_maintenance_operations that can take place during a state 
    public function state(){
    return $this->belongsTo(State::class) ; 
    }

    //Define the relation between a curative_maintenance_operation and the person who cheking it : an curative maintenance operation has only one qualityVerifier
    public function qualityVerifier(){
        return $this->belongsTo(User::class, 'qualityVerifier_id') ; 
    }

    //Define the relation between  a curative_maintenance_operation and the person who cheking it : a curative maintenance operation has only one technicalVerifier
    public function technicalVerifier(){
        return $this->belongsTo(User::class, 'technicalVerifier_id') ; 
    }

    //Define the relation between a curative maintenance operation and the person realizing it 
    public function RealizedBy(){
        return $this->belongsTo(User::class, 'realizedBy_id') ; 
    }

    //Define the relation between a curative maintenance operation and the person entering it 
    public function EnteredBy(){
        return $this->belongsTo(User::class, 'createdBy_id') ; 
    }
}
