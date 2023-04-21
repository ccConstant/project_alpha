<?php

/**
 * Filename: DocumentaryControl.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file defines the model DocumentaryControl. We can see more details about this model (like his attributes) in the
 * migration file named "2023_04_20_141240_create_documentary_controls_table.php"
 */

namespace App\Models\SW02;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentaryControl extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = ['docControl_name', 'docControl_reference', 'docControl_materialCertifSpe', 'incmInsp_id', 'docControl_FDS'];

    //Define the relation between a documentaryControl and its inspection: a documentaryControl has only one inspection
    public function inspection(){
        return $this->belongsTo(IncomingInspection::class, 'incmInsp_id') ;
    }
}
