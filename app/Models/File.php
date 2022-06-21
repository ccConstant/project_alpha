<?php

/*
* Filename : File.php
* Creation date : 9 May 2022
* Update date : 10 May 2022
* This file define the model File. We can see more details about this model (like his attributes) in the 
* migration file named "2022_05_10_064422_create_files_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EquipmentTemp ; 

class File extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['file_name', 'file_location', 'file_validate', 'equipmentTemp_id', 'mmeTemp_id'] ;

    //Define the relation between an equipment_temp and its files : a file can correspond to many equipment temps
    public function equipment_temps(){
        return $this->belongsTo(quipmentTemp::class, 'equipmentTemp_id') ; 
    }

}

