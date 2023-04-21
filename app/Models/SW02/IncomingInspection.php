<?php

/**
 * Filename: IncomingInspection.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file defines the model IncomingInspection. We can see more details about this model (like his attributes) in the
 * migration file named "2023_04_20_115421_create_incoming_inspections_table.php"
 */
namespace App\Models\SW02;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingInspection extends Model
{
    use HasFactory;

    //Data which can be added, updated or deleted by us in the database.
    protected $fillable = ['incmInsp_remarks', 'incmInsp_partMaterialComplianceCertificate', 'incmInsp_rawMaterialCertificate', 'incmInsp_qualityApproverId', 'incmInsp_technicalReviewerId', 'incmInsp_signatureDate', 'incmInsp_consFam_id', 'incmInsp_compFam_id', 'incmInsp_rawFam_id', 'incmInsp_validate'];

    //Define the relation between a consFamily and its incomingInspection: an incomingInspection can correspond to only one consFamily
    public function cons_family(){
        return $this->belongsTo(ConsFamily::class, 'incmInsp_consFam_id') ;
    }

    //Define the relation between a compFamily and its incomingInspection: an incomingInspection can correspond to only one compFamily
    public function comp_family(){
        return $this->belongsTo(CompFamily::class, 'incmInsp_compFam_id') ;
    }

    //Define the relation between a rawFamily and its incomingInspection: an incomingInspection can correspond to only one rawFamily
    public function raw_family(){
        return $this->belongsTo(RawFamily::class, 'incmInsp_rawFam_id') ;
    }

    //Define the relation between a incomingInspection and the user who reviewed it: an incomingInspection has only one technicalReviewer
    public function technical_reviewer(){
        return $this->belongsTo(User::class, 'incmInsp_technicalReviewerId') ;
    }

    //Define the relation between a incomingInspection and the user who approved it: an incomingInspection has only one qualityApprover
    public function quality_approver(){
        return $this->belongsTo(User::class, 'incmInsp_qualityApproverId') ;
    }
}
