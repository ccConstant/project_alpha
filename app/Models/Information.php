<?php

/*
* Filename : Information.php
* Creation date : 9 Jun 2022
* Update date : 9 Jun 2022
* This file define the model Information. We can see more details about this model (like his attributes) in the 
* migration file named "2022_06_09_080030_create_informations_table.php" in Database>migrations." 
* 
*/ 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['info_name', 'info_value', 'info_set'] ; 
}
