<?php

/*
* Filename: SupplierController.php
* Creation date: 20 Apr 2023
* Update date: 20 Apr 2023
* This file defines the model SupplierController. We can see more details about this model (like his attributes) in the
* migration file named "2023_04_20_110425_create_suppliers_table.php"
*/

namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\RawFamily;
use App\Models\User;

class Supplier extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = ['supplr_name', 'supplr_receptionNumber', 'supplr_formID', 'supplr_consFam_id', 'supplr_compFam_id', 'supplr_rawFam_id', 'supplr_agreementNumber', 'supplr_qualityCertificationNumber', 'supplr_specificInstructions', 'supplr_version', 'supplr_technicalReviewer_id', 'supplr_validate', 'supplr_signatureDate', 'supplr_siret', 'supplr_website', 'supplr_activity', 'supplr_real', 'supplr_VATNumber', 'supplr_critical', 'supplr_endLinkToFolder', 'supplr_active'];

    //Define the relation between a consFamily and its supplier: a supplier can correspond to only one consFamily
    public function cons_family(){
        return $this->belongsTo(ConsFamily::class, 'supplr_consFam_id') ;
    }

    //Define the relation between a compFamily and its supplier: a supplier can correspond to only one compFamily
    public function comp_family(){
        return $this->belongsTo(CompFamily::class, 'supplr_compFam_id') ;
    }

    //Define the relation between a rawFamily and its supplier: a supplier can correspond to only one rawFamily
    public function raw_family(){
        return $this->belongsTo(RawFamily::class, 'supplr_rawFam_id') ;
    }

    //Define the relation between a supplier and the user who reviewed it: a supplier has only one technicalReviewer
    public function technical_reviewer(){
        return $this->belongsTo(User::class, 'supplr_technicalReviewer_id') ;
    }

    // Define the relation between a supplier and his address: a supplier can have multiple address
    public function supplr_adr() {
        return $this->hasMany(SupplierAdr::class);
    }

    // Define the relation between a supplier and his contacts: a supplier can have multiple contacts
    public function supplr_contact() {
        return $this->hasMany(SupplierContact::class);
    }
}
