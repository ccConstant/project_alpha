<?php

/**
 * Filename: SupplierContact.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file defines the model supplierContact. We can see more details about this model (like his attributes) in the
 * migration file named "2023_04_20_120526_create_supplier_contacts_table.php"
 */

namespace App\Models\SW02;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW02\Supplier;

class SupplierContact extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = ['supplrContact_name', 'supplrContact_function', 'supplrContact_phoneNumber', 'supplrContact_email', 'supplr_id', 'supplrContact_validate', 'supplrContact_principal'];

    //Define the relation between a supplier and its supplierContacts: a supplier can have many supplierContacts
    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplr_id') ;
    }
}
