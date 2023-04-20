<?php

/*
* Filename : Risk.php
* Creation date : 9 May 2022
* Update date : 8 Feb 2023
* This file define the model Risk. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_063805_create_risks_table.php" in Database>migrations." 
* 
*/ 


namespace App\Models\SW01;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW01\EnumRiskFor ; 
use App\Models\SW01\EquipmentTemp ; 
use App\Models\SW01\PreventiveMaintenanceOperation ; 

class Risk extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['risk_remarks', 'risk_wayOfControl', 'risk_validate', 'enumRiskFor_id', 'equipmentTemp_id','preventiveMaintenanceOperation_id'] ; 

    //Define the relation between a risk and the risk target 
    public function enumRiskFor(){
        return $this->belongsTo(EnumRiskFor::class, 'enumRiskFor_id') ; 
    }

    //Define the relation between an equipment_temp and its risks : a risk can correspond to only one equipment temps
    public function equipment_temps(){
        return $this->belongsTo(EquipmentTemp::class, 'equipmentTemp_id') ; 
    }


    //Define the relation between a risk and the preventive maintenance operation which he is linked : a risk can be linked to only one preventive maintenance operation 
    public function preventive_maintenance_operation(){
        return $this->belongsTo(PreventiveMaintenanceOperation::class, 'preventiveMaintenanceOperation_id') ; 
    }

}
