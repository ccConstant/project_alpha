<?php

/*
* Filename : MmeTemp.php
* Creation date : 21 Jun 2022
* Update date : 8 Feb 2023
* This file define the model MmeTemp. We can see more details about this model (like his attributes) in the 
* migration file named "2022_06_07_144940_create_mme_temps_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models\SW01;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW01\MmeState;
use App\Models\SW01\Mme ;
use App\Models\User ;
use App\Models\File ;
use App\Models\SW01\Verification ; 
use App\Models\SW01\MmeUsage ;

class MmeTemp extends Model
{
    use HasFactory;

     //Data which can be added, updated or deleted by us in the data base.
     protected $fillable = ['mmeTemp_version', 'mmeTemp_date', 'mmeTemp_validate', 'mmeTemp_remarks', 'mme_id', 'qualityVerifier_id', 'technicalVerifier_id', 'createdBy_id', 'mmeTemp_lifeSheetCreated'] ; 

    
     //Define the relation between an mme_temp and its files : an mme_temp can have many files
      public function files(){
         return $this->hasMany(File::class) ; 
     }
 
     //Define the relation between an mme_temp and the mme which he is linked : an mme_temp can be linked to only one mme 
     public function mme(){
         return $this->belongsTo(Mme::class, 'mme_id') ; 
     }

     //Define the relation between an mme_temp and the person who cheking it : an mme_temp has only one qualityVerifier
     public function qualityVerifier(){
         return $this->belongsTo(User::class, 'qualityVerifier_id') ; 
     }
 
     //Define the relation between an mme_temp and the person who cheking it : an mme_temp has only one technicalVerifier
     public function technicalVerifier(){
         return $this->belongsTo(User::class, 'technicalVerifier_id') ; 
     }
 
     //Define the relation between an mme_temp and the person who creating it : an mme_temp has only one creator (person who makes the changes creating the mme_temp)
     public function createdBy(){
         return $this->belongsTo(User::class, 'createdBy_id') ; 
     }
 
     //Define the relation between an mme_temp and its states : an mme_temp can have many states
     public function states(){
         return $this->belongsToMany(MmeState::class, 'pivot_mme_temp_state', 'mmeTemp_id', 'mme_state_id') ; 
     }
 
     //Define the relation between a mme_temp and its verifications : a mme_temp can have many verifications
     public function verifications(){
         return $this->hasMany(Verification::class);
     }
 
     //Define the relation between an mme_temp and its usages : an mme_temp can have many usages
      public function usages(){
         return $this->hasMany(MmeUsage::class);
     }
 
}
