<?php

/*
* Filename : EnumStateName.php
* Creation date : 10 May 2022
* Update date : 10 May 2022
* This file define the model EnumStateName. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_061315_create_enum_state_names_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\State ; 

class EnumStateName extends Model
{
    use HasFactory;

     //Data which can be added, updated or deleted by us in the data base.
     protected $fillable = ['value'] ; 

     //Define the relation between a state and his name : a name can correspond to many states
    public function states(){
        return $this->hasMany(State::class) ; 
    }

}
