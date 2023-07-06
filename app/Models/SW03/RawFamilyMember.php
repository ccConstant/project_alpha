<?php

/*
* Filename : RawFamilyMember.php
* Creation date : 20 Apr 2023
* Update date : 5 Jul 2023
* This file define the model RawFamilyMember. We can see more details about this model (like his attributes) in the
* migration file named "2023_04_20_083900_create_raw_family_members_table.php" in Database>migrations."
*/

namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\RawFamily;
use App\Models\User;
use App\Models\SW03\RawSubFamily;

class RawFamilyMember extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable =  ['rawMb_ref',  'rawSubFam_id', 'rawMb_design', 'rawMb_validate' ] ;

    //Define the relation between a raw_family_member and the raw_family which he is linked : a raw_family_member can be linked to only one raw_family
    public function raw_sub_family(){
        return $this->belongsTo(RawSubFamily::class, 'rawSubFam_id') ;
    }

}
