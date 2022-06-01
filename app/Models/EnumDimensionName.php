<?php

/*
* Filename : EnumDimensionName.php
* Creation date : 9 May 2022
* Update date : 10 May 2022
* This file define the model EnumDimensionName. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_064118_create_enum_dimension_names_table.php" in Database>migrations.
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dimension ; 

class EnumDimensionName extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['value'] ; 

    //Define the relation between a dimension and her name : a name can correspond to many dimensions
    public function dimensions(){
        return $this->hasMany(Dimension::class) ; 
    }
}
