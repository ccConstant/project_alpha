<?php

/*
* Filename : EnumPowerType.php
* Creation date : 9 May 2022
* Update date : 10 May 2022
* This file define the model EnumPowerType. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_062934_create_enum_power_types_table.php" in Database>migrations.
* 
*/ 

namespace App\Models\SW01;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW01\Power ; 

class EnumPowerType extends Model
{
    use HasFactory;

     //Data which can be added, updated or deleted by us in the data base.
     protected $fillable = ['value'] ; 

    //Define the relation between a power and his type : a type can correspond to many powers
    public function powers(){
        return $this->hasMany(Power::class) ; 
    }
}
