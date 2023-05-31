<?php

namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawSubFamily extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = [
        'rawFam_genDesign',
        'rawFam_genRef',
        'rawFam_drawingPath',
        'rawFam_id',
        'rawFam_refExtension',
        'rawFam_designExtension',
    ];

    /*Define the relation between a raw_sub_family and the raw_family which he is linked:
    a raw_sub_family can be linked to only one raw_family*/
    public function rawFamily()
    {
        return $this->belongsTo(RawFamily::class, 'rawFam_id');
    }

    /*Define the relation between a raw_sub_family and its raw_family_members:
     a raw_family_members can correspond to only one raw_sub_family*/
    public function raw_family_members(){
        return $this->hasMany(RawFamilyMember::class) ;
    }
}
