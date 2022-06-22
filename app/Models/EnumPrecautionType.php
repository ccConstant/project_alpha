<?php

/*
* Filename : EnumPrecautionType.php
* Creation date : 9 Jun 2022
* Update date : 9 Jun 2022
* This file define the model EnumPrecautionType. We can see more details about this model (like his attributes) in the 
* migration file named "2022_06_09_073622_create_enum_precaution_types_table.php" in Database>migrations.
* 
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Precaution  ;

class EnumPrecautionType extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['value'] ; 

    //Define the relation between a precaution and his type 
   public function precautions(){
       return $this->hasMany(Precaution::class) ; 
   }
}
