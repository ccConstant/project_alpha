<?php

/*
* Filename : PreventiveMaintenanceOperationRealized.php
* Creation date : 10 May 2022
* Update date : 10 May 2022
* This file define the model PreventiveMaintenanceOperationRealized. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_062745_create_preventive_maintenance_operation_realizeds_table.php" in Database>migrations." 
* 
*/ 



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\State ;
use App\Models\People ; 
use App\Models\PreventiveMaintenanceOperation ;  

class PreventiveMaintenanceOperationRealized extends Model
{
    use HasFactory;

     //Data which can be added, updated or deleted by us in the data base.
     protected $fillable = ['prvMtnOpRlz_reportNumber', 'prvMtnOpRlz_startDate', 'prvMtnOpRlz_endDate', 'prvMtnOpRlz_entryDate', 'prvMtnOpRlz_validate', 'enteredBy_id', 'realizedBy_id', 'state_id', 'prvMtnOp_id'];
    //Define the relation between an preventive_maintenance_operation_realized and the state during wich she is realized
    public function state(){
        return $this->belongsTo(State::class, 'state_id') ; 
    }

     //Define the relation between a preventive maintenance operation realized and the person realizing it 
     public function realizedBy(){
        return $this->belongsTo(People::class, 'realizedBy_id') ; 
    }

    //Define the relation between a preventive maintenance operation realized and the person entering it 
    public function enteredBy(){
        return $this->belongsTo(People::class, 'enteredBy_id') ; 
    }

    //Define the relation between a preventive maintenance operation realized and the preventive maintenance operation which she is corresponding
    public function preventiveMaintenanceOperation(){
        return $this->belongsTo(PreventiveMaintenanceOperation::class, 'prvMtnOp_id') ; 
    }

}
