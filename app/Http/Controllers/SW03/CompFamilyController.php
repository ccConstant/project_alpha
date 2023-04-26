<?php

/*
* Filename : CompFamilyController.php 
* Creation date : 25 Apr 2023
* Update date : 25 Apr 2023
* This file is used to link the view files and the database that concern the comp family table. 
* For example : add a comp family in the data base, update a comp family...
*/ 

namespace App\Http\Controllers\SW03;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;

class CompFamilyController extends Controller
{
    /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for check data with the route : /comp/family/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_compFamily(Request $request){

        //-----CASE compFam->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his compFam, so he must enter all the attributes
        if ($request->compFam_validate=='validated'){
            $this->validate(
                $request,
                [
                    'compFam_ref' => 'required|min:3|max:255',
                    'compFam_design' => 'required|min:3|max:255',
                    'compFam_drawingPath' => 'required|min:3|max:255',
                    'compFam_variablesCharac' => 'required|min:3|max:255',
                    'compFam_version' => 'required|min:3|max:255',
                ],
                [
                    'compFam_ref.required' => 'You must enter a reference for your comp family ',
                    'compFam_ref.min' => 'You must enter at least three characters ',
                    'compFam_ref.max' => 'You must enter a maximum of 255 characters',
                    'compFam_design.required' => 'You must enter a designation for your comp family ',
                    'compFam_design.min' => 'You must enter at least three characters ',
                    'compFam_design.max' => 'You must enter a maximum of 255 characters',
                    'compFam_drawingPath.required' => 'You must enter a drawing path for your comp family ',
                    'compFam_drawingPath.min' => 'You must enter at least three characters ',
                    'compFam_drawingPath.max' => 'You must enter a maximum of 255 characters',
                    'compFam_variablesCharac.required' => 'You must enter variables characteristics for your comp family ',
                    'compFam_variablesCharac.min' => 'You must enter at least three characters ',
                    'compFam_variablesCharac.max' => 'You must enter a maximum of 255 characters',
                    'compFam_version.required' => 'You must enter a version for your comp family ',
                    'compFam_version.min' => 'You must enter at least three characters ',
                    'compFam_version.max' => 'You must enter a maximum of 255 characters',
                ]
            );

            if ($request->compFam_purchasedBy==NULL){
                return response()->json([
                    'errors' => [
                        'compFam_purchasedBy' => ['You must reference who purchased this comp family'],
                    ],
                ], 422);
            }

           
        }else{
             //-----CASE compFam->validate=drafted or compFam->validate=to be validated----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations 
            $this->validate(
                $request,
                [
                    'compFam_ref' => 'required|min:3|max:255',
                    'compFam_design' => 'required|min:3|max:255',
                    'compFam_drawingPath' => 'max:255',
                    'compFam_variablesCharac' => 'max:255',
                    'compFam_version' => 'max:255',
                ],
                [
                    'compFam_ref.required' => 'You must enter a reference for your comp family ',
                    'compFam_ref.min' => 'You must enter at least three characters ',
                    'compFam_ref.max' => 'You must enter a maximum of 255 characters',
                    'compFam_design.required' => 'You must enter a designation for your comp family ',
                    'compFam_design.min' => 'You must enter at least three characters ',
                    'compFam_design.max' => 'You must enter a maximum of 255 characters',
                    'compFam_drawingPath.max' => 'You must enter a maximum of 255 characters',
                    'compFam_variablesCharac.max' => 'You must enter a maximum of 255 characters',
                    'compFam_version.max' => 'You must enter a maximum of 255 characters',
                   
                ]
            );
        }
    }

    /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for insert with the route : /comp/family/add (post)
     * Add a new enregistrement of comp family in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new comp family
     */
    public function add_compFamily(Request $request){

        //Creation of a new compFam
        $compFamily=compFamily::create([
            'compFam_ref' => $request->compFam_ref,
            'compFam_design' => $request->compFam_design,
            'compFam_drawingPath'=> $request->compFam_drawingPath,
            'compFam_purchasedBy' => $request->compFam_purchasedBy,
            'compFam_variablesCharac' => $request->compFam_variablesCharac,
            'compFam_validate' => $request->compFam_validate,
            'compFam_version' => $request->compFam_version,
            'compFam_active' => $request->compFam_active,
        ]) ; 

        //$compFamily_id=$compFamily->id ;
        
        //return response()->json($compFamily_id) ;
    }
}
