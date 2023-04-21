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

class SupplierAdr extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = ['supplierAdr_street', 'supplierAdr_town', 'supplierAdr_country', 'supplier_id', 'supplierAdr_validate', 'supplierAdr_name', 'supplierAdr_principal'];

    //Define the relation between a supplierAdr and its supplier: a supplierAdr can correspond to only one supplier
    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id') ;
    }
}
