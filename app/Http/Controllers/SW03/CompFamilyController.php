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
use Illuminate\Support\Facades\DB;
use App\Models\SW03\EnumPurchasedBy;

class CompFamilyController extends Controller
{
    /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for check data with the route : /comp/family/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_compFamily(Request $request){

        //-----CASE compFam->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his compFam, so he must enter all the attributes
        if ($request->artFam_validate=='validated'){
            $this->validate(
                $request,
                [
                    'artFam_ref' => 'required|min:3|max:255|String',
                    'artFam_design' => 'required|min:3|max:255|String',
                    'artFam_drawingPath' => 'required|min:3|max:255|String',
                    'artFam_variablesCharac' => 'required|min:2|max:255|String',
                    'artFam_version' => 'required|min:2|max:4|String',
                ],
                [

                    'artFam_ref.required' => 'You must enter a reference for your comp family ',
                    'artFam_ref.min' => 'You must enter at least three characters ',
                    'artFam_ref.max' => 'You must enter less than 255 characters ',
                    'artFam_ref.String' => 'You must enter a string ',
                    'artFam_design.required' => 'You must enter a design for your comp family ',
                    'artFam_design.min' => 'You must enter at least three characters ',
                    'artFam_design.max' => 'You must enter less than 255 characters ',
                    'artFam_design.String' => 'You must enter a string ',
                    'artFam_drawingPath.required' => 'You must enter a drawing path for your comp family ',
                    'artFam_drawingPath.min' => 'You must enter at least three characters ',
                    'artFam_drawingPath.max' => 'You must enter less than 255 characters ',
                    'artFam_drawingPath.String' => 'You must enter a string ',
                    'artFam_variablesCharac.required' => 'You must enter variables characteristics for your comp family ',
                    'artFam_variablesCharac.min' => 'You must enter at least two characters ',
                    'artFam_variablesCharac.max' => 'You must enter less than 255 characters ',
                    'artFam_variablesCharac.String' => 'You must enter a string ',
                    'artFam_version.required' => 'You must enter a version for your comp family ',
                    'artFam_version.min' => 'You must enter at least two characters ',
                    'artFam_version.max' => 'You must enter less than 4 characters ',
                    'artFam_version.String' => 'You must enter a string ',
                ]
            );

            if ($request->artFam_purchasedBy==NULL || $request->artFam_purchasedBy==""){
                return response()->json([
                    'errors' => [
                        'artFam_purchasedBy' => ['You must reference who purchased this comp family'],
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
                    'artFam_version' => 'max:4|String',
                ],
                [
                    'artFam_ref.required' => 'You must enter a reference for your comp family ',
                    'artFam_ref.min' => 'You must enter at least three characters ',
                    'artFam_ref.max' => 'You must enter a maximum of 255 characters',
                    'artFam_ref.String' => 'You must enter a string ',
                    'artFam_design.required' => 'You must enter a designation for your comp family ',
                    'artFam_design.min' => 'You must enter at least three characters ',
                    'artFam_design.max' => 'You must enter a maximum of 255 characters',
                    'artFam_design.String' => 'You must enter a string ',
                    'artFam_drawingPath.max' => 'You must enter a maximum of 255 characters',
                    'artFam_drawingPath.String' => 'You must enter a string ',
                    'artFam_variablesCharac.max' => 'You must enter a maximum of 255 characters',
                    'artFam_variablesCharac.String' => 'You must enter a string ',
                    'artFam_version.max' => 'You must enter a maximum of 4 characters',
                    'artFam_version.String' => 'You must enter a string ',

                ]
            );
        }

         //we checked if the reference entered is already used for another component family
         $component_already_exist=CompFamily::where('compFam_ref', '=', $request->artFam_ref, 'and')->where('id', '<>', $request->artFam_id)->first() ;
         if ($component_already_exist!=null){
             return response()->json([
                 'errors' => [
                     'artFam_ref' => ["This reference is already use for another comp family"]
                 ]
             ], 429);
         }
    }

    /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for insert with the route : /comp/family/add (post)
     * Add a new enregistrement of comp family in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new comp family
     */
    public function add_compFamily(Request $request){
        $enum=NULL;
        if ($request->artFam_purchasedBy!="" && $request->artFam_purchasedBy!=NULL){
            $enum=EnumPurchasedBy::where('value', '=', $request->artFam_purchasedBy)->first() ;
            $enum=$enum->id ;
        }

        //Creation of a new compFam
        $compFamily=compFamily::create([
            'compFam_ref' => $request->artFam_ref,
            'compFam_design' => $request->artFam_design,
            'compFam_drawingPath'=> $request->artFam_drawingPath,
            'enumPurchasedBy_id' => $enum,
            'compFam_variablesCharac' => $request->artFam_variablesCharac,
            'compFam_validate' => $request->artFam_validate,
            'compFam_version' => $request->artFam_version,
            'compFam_active' => $request->artFam_active,
        ]) ;

        $compFamily_id=$compFamily->id ;

        return response()->json($compFamily_id) ;
    }

    public function send_compFamilies(){
        $compFamilies=CompFamily::all() ;
        $array = [];
        foreach ($compFamilies as $compFamily) {
            $obj = [
                'id' => $compFamily->id,
                'compFam_ref' => $compFamily->compFam_ref,
                'compFam_design' => $compFamily->compFam_design,
                'compFam_drawingPath' => $compFamily->compFam_drawingPath,
                'compFam_variablesCharac' => $compFamily->compFam_variablesCharac,
                'compFam_version' => $compFamily->compFam_version,
                'compFam_nbrVersion' => $compFamily->compFam_nbrVersion,
                'compFam_validate' => $compFamily->compFam_validate,
                'compFam_active' => $compFamily->compFam_active
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }
}
