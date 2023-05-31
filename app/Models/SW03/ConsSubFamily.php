<?php

namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsSubFamily extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = [
        'consFam_genDesign',
        'consFam_genRef',
        'consFam_drawingPath',
        'consFam_id',
        'consFam_refExtension',
        'consFam_designExtension',
    ];

    /*Define the relation between a cons_sub_family and the cons_family which he is linked:
    a cons_sub_family can be linked to only one cons_family*/
    public function consFamily()
    {
        return $this->belongsTo(ConsFamily::class, 'consFam_id');
    }

    /*Define the relation between a cons_sub_family and its cons_family_members:
     a cons_family_members can correspond to only one cons_sub_family*/
    public function cons_family_members(){
        return $this->hasMany(ConsFamilyMember::class) ;
    }
}
