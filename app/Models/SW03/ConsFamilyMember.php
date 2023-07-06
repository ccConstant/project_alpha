<?php

/*
* Filename : ConsFamilyMember.php
* Creation date : 20 Apr 2023
* Update date : 20 Apr 2023
* This file define the model ConsFamilyMember. We can see more details about this model (like his attributes) in the
* migration file named "2023_04_20_080526_create_cons_family_members_table.php" in Database>migrations."
*
*/

namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\ConsSubFamily;
use App\Models\User;

class ConsFamilyMember extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the data base.
    protected $fillable = ['consMb_ref',  'consSubFam_id', 'consMb_design', 'consMb_validate' ] ;

    //Define the relation between a cons_family_member and the cons_family which he is linked : a cons_family_member can be linked to only one cons_family
    public function cons_sub_family(){
        return $this->belongsTo(ConsSubFamily::class, 'consSubFam_id') ;
    }

}
