<?php

/*
* Filename : MmeState.php
* Creation date : 7 Jun 2022
* Update date : 8 Feb 2023
* This file define the model State. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_062315_create_mme_states_table.php".
* 
*/ 

namespace App\Models\SW01;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW01\CurativeMaintenanceOperation ;
use App\Models\SW01\VerificationRealized ;
use App\Models\SW01\MmeTemp ;
use App\Models\SW01\Mme;

class MmeState extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['state_remarks', 'state_startDate', 'state_endDate', 'state_isOk', 'state_validate', 'state_name', 'reformedBy_id'] ; 
    
    //Define the relation between mme and state : only one equipment can be created during a state
    public function mme(){
        return $this->hasOne(Mme::class) ; 
    }

    //Define the relation between a state and the verifications_realizeds that can take place during the state
    public function verifications_realizeds(){
        return $this->hasMany(VerificationRealized::class) ; 
    }

    //Define the relation between a state and the person who reformed the equipment during the state 
    public function reformedBy(){
        return $this->hasOne(People::class, 'reformedBy_id') ; 
    }

    //Define the relation between an mme_temp and its state : a state can correspond to many equipment temps
    public function mme_temps(){
        return $this->belongsToMany(MmeTemp::class, 'pivot_mme_temp_state', 'mme_state_id', 'mmeTemp_id') ; 
    }

    //Define the relation between a state and the curative_maintenance_operations that can take place during the state
    public function curative_maintenance_operations(){
        return $this->hasMany(CurativeMaintenanceOperation::class) ; 
    }
}
