<?php

/**
 * Filename: SupplierAdr.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file defines the model SupplierAdr. We can see more details about this model (like his attributes) in the
 * migration file named "2023_04_20_121202_create_supplier_adrs_table.php"
 */

namespace App\Models\SW02;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW02\Supplier;

class SupplierAdr extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = ['supplrAdr_street', 'supplrAdr_town', 'supplrAdr_country', 'supplr_id', 'supplrAdr_validate', 'supplrAdr_name', 'supplrAdr_principal'];

    //Define the relation between a supplierAdr and its supplier: a supplierAdr can correspond to only one supplier
    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplr_id') ;
    }
}
