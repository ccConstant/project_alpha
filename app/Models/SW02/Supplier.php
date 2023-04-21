<?php

/*
* Filename: Supplier.php
* Creation date: 20 Apr 2023
* Update date: 20 Apr 2023
* This file defines the model Supplier. We can see more details about this model (like his attributes) in the
* migration file named "2023_04_20_110425_create_suppliers_table.php"
*/

namespace App\Models\SW02;

use App\Models\User;
use App\Models\SW02\CompFamily;
use App\Models\SW02\ConsFamily;
use App\Models\SW02\RawFamily;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = ['supplier_name', 'supplier_receptionNumber', 'supplier_formID', 'supplier_consFam_id', 'supplier_compFam_id', 'supplier_rawFam_id', 'supplier_agreementNumber', 'supplier_qualityCertificationNumber', 'supplier_specificInstructions', 'supplier_version', 'technicalReviewer_id', 'supplier_validate', 'supplier_signatureDate', 'supplier_siret', 'supplier_website', 'supplier_activity', 'supplier_Real', 'supplier_VATNumber', 'supplier_critical', 'supplier_endLinkToFolder', 'supplier_active'];

    //Define the relation between a consFamily and its supplier: a supplier can correspond to only one consFamily
    public function cons_family(){
        return $this->belongsTo(ConsFamily::class, 'supplier_consFam_id') ;
    }

    //Define the relation between a compFamily and its supplier: a supplier can correspond to only one compFamily
    public function comp_family(){
        return $this->belongsTo(CompFamily::class, 'supplier_compFam_id') ;
    }

    //Define the relation between a rawFamily and its supplier: a supplier can correspond to only one rawFamily
    public function raw_family(){
        return $this->belongsTo(RawFamily::class, 'supplier_rawFam_id') ;
    }

    //Define the relation between a supplier and the user who reviewed it: a supplier has only one technicalReviewer
    public function technical_reviewer(){
        return $this->belongsTo(User::class, 'technicalReviewer_id') ;
    }
}
