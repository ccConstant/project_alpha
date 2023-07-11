<?php

/**
 * Filename: AdminControl.php
 * Creation date: 10 Jul 2023
 * Update date: 10 Jul 2023
 * This file defines the model AdminControl. We can see more details about this model (like his attributes) in the
 * migration file named "2023_07_10_140119_create_admin_controls_table.php"
 */

 namespace App\Models\SW03;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SW03\IncomingInspection;

class AdminControl extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = ['purSpe_id','adminControl_name', 'adminControl_reference', 'adminControl_materialCertifSpe', 'incmgInsp_id', 'adminControl_FDS'];

    //Define the relation between a adminControl and its inspection: a adminControl has only one inspection
    public function incomingInspection(){
        return $this->belongsTo(IncomingInspection::class, 'incmgInsp_id') ;
    }

    //Define the relation between a adminControl and his files: a adminControl has many files
    public function files(){
        return $this->hasMany(File::class, 'docControl_id') ;
    }
}

