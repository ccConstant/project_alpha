<?php

/*
* Filename : History.php
* Creation date : 18 Jan 2023
* Update date : 8 Feb 2023
* This file define the model History. We can see more details about this model (like his attributes) in the 
* migration file named "2023_01_18_073912_create_histories_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW01\EquipmentTemp ;
use App\Models\SW01\MmeTemp ; 

class History extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['history_numVersion', 'history_reasonUpdate', 'equipmentTemp_id', 'mmeTemp_id'] ;

    //Define the relation between an equipment_temp and its histories : an equipment temps can correspond to many histories
    public function equipment_temps(){
        return $this->belongsTo(EquipmentTemp::class, 'equipmentTemp_id') ; 
    }

    //Define the relation between an mme_temp and its histories : an mme temp can correspond to many histories
    public function mme_temps(){
        return $this->belongsTo(MmeTemp::class, 'mmeTemp_id') ; 
    }
}
