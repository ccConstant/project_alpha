<?php

/*
* Filename : CompFamilyMember.php
* Creation date : 20 Apr 2023
* Update date : 5 Jul 2023
* This file define the model CompFamilyMember. We can see more details about this model (like his attributes) in the
* migration file named "2023_04_20_082307_create_comp_family_members_table.php" in Database>migrations."
*/

namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\CompSubFamily;

class CompFamilyMember extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['compMb_ref',  'compSubFam_id', 'compMb_design', 'compMb_validate' ] ;

    //Define the relation between a comp_family_member and the comp_sub_family which he is linked : a comp_family_member can be linked to only one comp_sub_family
    public function comp_sub_family(){
        return $this->belongsTo(CompSubFamily::class, 'compSubFam_id') ;
    }

}
