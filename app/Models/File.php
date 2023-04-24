<?php

/*
* Filename : File.php
* Creation date : 10 May 2022
* Update date : 8 Feb 2023
* This file define the model File. We can see more details about this model (like his attributes) in the
* migration file named "2022_05_10_064422_create_files_table.php" in Database>migrations."
*
*/

namespace App\Models;

use App\Models\SW03\AspectTest;
use App\Models\SW03\ComplementaryTest;
use App\Models\SW03\DimensionalTest;
use App\Models\SW03\DocumentaryControl;
use App\Models\SW03\FunctionalTest;
use App\Models\SW03\PurchaseSpecification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW01\EquipmentTemp ;
use App\Models\SW01\MmeTemp ;

class File extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['file_name', 'file_location', 'file_validate', 'equipmentTemp_id', 'mmeTemp_id', 'documentaryControl_id', 'aspectTest_id', 'functionalTest_id', 'dimensionalTest_id', 'complementaryTest_id', 'purchaseSpec_id'] ;

    //Define the relation between an equipment_temp and its files : a file can correspond to only one equipment temp
    public function equipment_temps(){
        return $this->belongsTo(EquipmentTemp::class, 'equipmentTemp_id') ;
    }

    //Define the relation between an mme_temp and its files : a file can correspond to only one mme temp
    public function mme_temps(){
        return $this->belongsTo(MmeTemp::class, 'mmeTemp_id') ;
    }

    //Define the relation between a documentary control and its files : a file can correspond to only one documentary control
    public function documentary_controls(){
        return $this->belongsTo(DocumentaryControl::class, 'documentaryControl_id');
    }

    //Define the relation between an aspect test and its files : a file can correspond to only one aspect test
    public function aspect_tests(){
        return $this->belongsTo(AspectTest::class, 'aspectTest_id');
    }

    //Define the relation between a functional test and its files : a file can correspond to only one functional test
    public function functional_tests(){
        return $this->belongsTo(FunctionalTest::class, 'functionalTest_id');
    }

    //Define the relation between a dimensional test and its files : a file can correspond to only one dimensional test
    public function dimensional_tests(){
        return $this->belongsTo(DimensionalTest::class, 'dimensionalTest_id');
    }

    //Define the relation between a complementary test and its files : a file can correspond to only one complementary test
    public function complementary_tests(){
        return $this->belongsTo(ComplementaryTest::class, 'complementaryTest_id');
    }

    //Define the relation between a purchase specification and its files : a file can correspond to only one purchase specification
    public function purchase_specifications(){
        return $this->belongsTo(PurchaseSpecification::class, 'purchaseSpec_id');
    }
}

