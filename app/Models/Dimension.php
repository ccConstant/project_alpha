<?php

/*
* Filename : Dimension.php
* Creation date : 9 May 2022
* Update date : 9 May 2022
* This file define the model Dimension. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_064313_create_dimensions_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EnumDimensionType ; 
use App\Models\EnumDimensionName ; 
use App\Models\EnumDimensionUnit ;  
use App\Models\EquipmentTemp ; 

class Dimension extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['dim_value', 'dim_validate', 'enumDimensionType_id', 'enumDimensionName_id','enumDimensionUnit_id', 'equipmentTemp_id'];

    //Define the relation between a dimension and her type : a dimension can have only one type
    public function enumDimensionType(){
        return $this->belongsTo(EnumDimensionType::class, 'enumDimensionType_id') ; 
    }

    //Define the relation between a dimension and her name : a dimension can have only one name
    public function enumDimensionName(){
        return $this->belongsTo(EnumDimensionName::class, 'enumDimensionName_id') ; 
    }

    //Define the relation between a dimension and her unit : a dimension can have only one unit
    public function enumDimensionUnit(){
        return $this->belongsTo(EnumDimensionUnit::class, 'enumDimensionUnit_id') ; 
    }

    //Define the relation between an equipment_temp and its dimensions : a dimension can correspond to many equipment temps
    public function equipment_temps(){
        return $this->belongsTo(EquipmentTemp::class, 'equipmentTemp_id') ; 
    }
}
