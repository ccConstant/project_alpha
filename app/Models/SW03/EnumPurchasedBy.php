<?php

/*
* Filename : EnumPurchasedBy.php
* Creation date : 26 Apr 2023
* Update date : 26 Apr 2023
* This file define the model EnumPurchasedBy. We can see more details about this model (like his attributes) in the
* migration file named "2023_04_26_074616_create_enum_purchased_bies_table.php" in Database>migrations."
*
*/

namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\RawFamily;

class EnumPurchasedBy extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['value', 'caduc'] ;

    //Define the relation between an EnumPurchasedBy and its compFamily : an EnumPurchasedBy can have many compFamily
    public function compFamily(){
        return $this->belongsToMany(CompFamily::class, 'pivot_comp_fam_enum_purchased_by', 'purchaseBy_id', 'compFam_id') ;
    }

    //Define the relation between an EnumPurchasedBy and its consFamily : an EnumPurchasedBy can have many consFamily
    public function consFamily(){
        return $this->belongsToMany(ConsFamily::class, 'pivot_cons_fam_enum_purchased_by', 'purchaseBy_id', 'consFam_id') ;
    }

    //Define the relation between an EnumPurchasedBy and its rawFamily : an EnumPurchasedBy can have many rawFamily
    public function rawFamily(){
        return $this->belongsToMany(RawFamily::class, 'pivot_raw_fam_enum_purchased_by', 'purchaseBy_id', 'rawFam_id') ;
    }
}

