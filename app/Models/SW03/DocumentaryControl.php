<?php

/**
 * Filename: DocumentaryControl.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file defines the model DocumentaryControl. We can see more details about this model (like his attributes) in the
 * migration file named "2023_04_20_141240_create_documentary_controls_table.php"
 */

namespace App\Models\SW03;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\IncomingInspection;

class DocumentaryControl extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = ['docControl_name', 'docControl_reference', 'docControl_materialCertifSpe', 'incmgInsp_id', 'docControl_FDS'];

    //Define the relation between a documentaryControl and its inspection: a documentaryControl has only one inspection
    public function incomingInspection(){
        return $this->belongsTo(IncomingInspection::class, 'incmgInsp_id') ;
    }

    //Define the relation between a documentaryControl and his files: a documentaryControl has many files
    public function files(){
        return $this->hasMany(File::class, 'docControl_id') ;
    }
}
