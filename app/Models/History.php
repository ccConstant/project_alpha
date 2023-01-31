<?php

/*
* Filename : History.php
* Creation date : 18 Jan 2023
* Update date : 18 Jan 2023
* This file define the model History. We can see more details about this model (like his attributes) in the 
* migration file named "2023_01_18_073912_create_histories_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['history_numVersion', 'history_reasonUpdate', 'equipmentTemp_id', 'mmeTemp_id'] ;

}
