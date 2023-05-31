<?php

namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompSubFamily extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = [
        'compFam_genDesign',
        'compFam_genRef',
        'compFam_drawingPath',
        'compFam_id',
        'compFam_refExtension',
        'compFam_designExtension',
    ];

    /*Define the relation between a comp_sub_family and the comp_family which he is linked:
    a comp_sub_family can be linked to only one comp_family*/
    public function compFamily()
    {
        return $this->belongsTo(CompFamily::class, 'compFam_id');
    }

    /*Define the relation between a comp_sub_family and its comp_family_members:
     a comp_family_members can correspond to only one comp_sub_family*/
    public function comp_family_members(){
        return $this->hasMany(CompFamilyMember::class) ;
    }
}
