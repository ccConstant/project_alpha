<?php

/*
* Filename : EnumDimensionUnit.php
* Creation date : 9 May 2022
* Update date : 9 May 2022
* This file define the model EnumDimensionUnit. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_064209_create_enum_dimension_units_table.php" in Database>migrations.
* 
*/ 

namespace App\Models\SW01;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW01\Dimension ; 

class EnumDimensionUnit extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['value'] ; 

    //Define the relation between a dimension and her unit : a unit can correspond to many dimensions
    public function dimensions(){
        return $this->hasMany(Dimension::class) ; 
    }
}
