<?php

/*
* Filename : RawFamilyController.php 
* Creation date : 28 Apr 2023
* Update date : 28 Apr 2023
* This file is used to link the view files and the database that concern the raw family table. 
* For example : add a raw family in the data base, update a raw family...
*/ 

namespace App\Http\Controllers\SW03;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SW03\RawFamily;
use Illuminate\Support\Facades\DB;
use App\Models\SW03\EnumPurchasedBy;

class RawFamilyController extends Controller
{
    /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for check data with the route : /raw/family/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_rawFamily(Request $request){

        //-----CASE rawFam->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his rawFam, so he must enter all the attributes
        if ($request->artFam_validate=='validated'){
            $this->validate(
                $request,
                [
                    'artFam_ref' => 'required|min:3|max:255|String',
                    'artFam_design' => 'required|min:3|max:255|String',
                    'artFam_drawingPath' => 'required|min:3|max:255|String',
                    'artFam_variablesCharac' => 'required|min:2|max:255|String',
                ],
                [
                    
                    'artFam_ref.required' => 'You must enter a reference for your raw family ',
                    'artFam_ref.min' => 'You must enter at least three characters ',
                    'artFam_ref.max' => 'You must enter less than 255 characters ',
                    'artFam_ref.String' => 'You must enter a string ',
                    'artFam_design.required' => 'You must enter a design for your raw family ',
                    'artFam_design.min' => 'You must enter at least three characters ',
                    'artFam_design.max' => 'You must enter less than 255 characters ',
                    'artFam_design.String' => 'You must enter a string ',
                    'artFam_drawingPath.required' => 'You must enter a drawing path for your raw family ',
                    'artFam_drawingPath.min' => 'You must enter at least three characters ',
                    'artFam_drawingPath.max' => 'You must enter less than 255 characters ',
                    'artFam_drawingPath.String' => 'You must enter a string ',
                    'artFam_variablesCharac.required' => 'You must enter variables characteristics for your raw family ',
                    'artFam_variablesCharac.min' => 'You must enter at least two characters ',
                    'artFam_variablesCharac.max' => 'You must enter less than 255 characters ',
                    'artFam_variablesCharac.String' => 'You must enter a string ',
                ]
            );

            if ($request->artFam_purchasedBy==NULL || $request->artFam_purchasedBy==""){
                return response()->json([
                    'errors' => [
                        'artFam_purchasedBy' => ['You must reference who purchased this raw family'],
                    ],
                ], 422);
            }


           
        }else{
             //-----CASE artFam->validate=drafted or artFam->validate=to be validated----//
            //if the user has choosen "drafted" or "to be validated" he have no obligations 
            $this->validate(
                $request,
                [
                    'artFam_ref' => 'required|min:3|max:255|String',
                    'artFam_design' => 'required|min:3|max:255|String',
                    'artFam_drawingPath' => 'max:255|String',
                    'artFam_variablesCharac' => 'max:255|String',
                ],
                [
                    'artFam_ref.required' => 'You must enter a reference for your raw family ',
                    'artFam_ref.min' => 'You must enter at least three characters ',
                    'artFam_ref.max' => 'You must enter a maximum of 255 characters',
                    'artFam_ref.String' => 'You must enter a string ',
                    'artFam_design.required' => 'You must enter a designation for your raw family ',
                    'artFam_design.min' => 'You must enter at least three characters ',
                    'artFam_design.max' => 'You must enter a maximum of 255 characters',
                    'artFam_design.String' => 'You must enter a string ',
                    'artFam_drawingPath.max' => 'You must enter a maximum of 255 characters',
                    'artFam_drawingPath.String' => 'You must enter a string ',
                    'artFam_variablesCharac.max' => 'You must enter a maximum of 255 characters',
                    'artFam_variablesCharac.String' => 'You must enter a string ',
                   
                ]
            );
        }

         //we checked if the reference entered is already used for another raw family
         $raw_already_exist=RawFamily::where('rawFam_ref', '=', $request->artFam_ref, 'and')->where('id', '<>', $request->artFam_id)->first() ; 
         if ($raw_already_exist!=null){
             return response()->json([
                 'errors' => [
                     'artFam_ref' => ["This reference is already use for another raw family"]
                 ]
             ], 429);
         }
    }

    /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for insert with the route : /raw/family/add (post)
     * Add a new enregistrement of raw family in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : id of the new raw family
     */
    public function add_rawFamily(Request $request){
        $enum=NULL;
        if ($request->artFam_purchasedBy!="" && $request->artFam_purchasedBy!=NULL){
            $enum=EnumPurchasedBy::where('value', '=', $request->artFam_purchasedBy)->first() ;
            $enum=$enum->id ;
        }

        //Creation of a new rawFam
        $rawFamily=rawFamily::create([
            'rawFam_ref' => $request->artFam_ref,
            'rawFam_design' => $request->artFam_design,
            'rawFam_drawingPath'=> $request->artFam_drawingPath,
            'enumPurchasedBy_id' => $enum,
            'rawFam_variablesCharac' => $request->artFam_variablesCharac,
            'rawFam_validate' => $request->artFam_validate,
            'rawFam_active' => $request->artFam_active,
        ]) ; 

        $rawFamily_id=$rawFamily->id ;
        
        return response()->json($rawFamily_id) ;
    }
    
}
