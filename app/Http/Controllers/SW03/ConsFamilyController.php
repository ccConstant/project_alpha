<?php

/*
* Filename : ConsFamilyController.php
* Creation date : 2 May 2023
* Update date : 2 May 2023
* This file is used to link the view files and the database that concern the cons family table.
* For example : add a cons family in the data base, update a cons family...
*/


namespace App\Http\Controllers\SW03;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SW03\ConsFamily;
use Illuminate\Support\Facades\DB;
use App\Models\SW03\EnumPurchasedBy;

class ConsFamilyController extends Controller
{
    /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for check data with the route : /cons/family/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_consFamily(Request $request){

        //-----CASE consFam->validate=validated----//
        //if the user has choosen "validated" value that's mean he wants to validate his consFam, so he must enter all the attributes
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

                    'artFam_ref.required' => 'You must enter a reference for your cons family ',
                    'artFam_ref.min' => 'You must enter at least three characters ',
                    'artFam_ref.max' => 'You must enter less than 255 characters ',
                    'artFam_ref.String' => 'You must enter a string ',
                    'artFam_design.required' => 'You must enter a design for your cons family ',
                    'artFam_design.min' => 'You must enter at least three characters ',
                    'artFam_design.max' => 'You must enter less than 255 characters ',
                    'artFam_design.String' => 'You must enter a string ',
                    'artFam_drawingPath.required' => 'You must enter a drawing path for your cons family ',
                    'artFam_drawingPath.min' => 'You must enter at least three characters ',
                    'artFam_drawingPath.max' => 'You must enter less than 255 characters ',
                    'artFam_drawingPath.String' => 'You must enter a string ',
                    'artFam_variablesCharac.required' => 'You must enter variables characteristics for your cons family ',
                    'artFam_variablesCharac.min' => 'You must enter at least two characters ',
                    'artFam_variablesCharac.max' => 'You must enter less than 255 characters ',
                    'artFam_variablesCharac.String' => 'You must enter a string ',
                    'artFam_version.required' => 'You must enter a version for your cons family ',
                    'artFam_version.min' => 'You must enter at least two characters ',
                    'artFam_version.max' => 'You must enter less than 4 characters ',
                    'artFam_version.String' => 'You must enter a string ',
                ]
            );

            if ($request->artFam_purchasedBy==NULL || $request->artFam_purchasedBy==""){
                return response()->json([
                    'errors' => [
                        'artFam_purchasedBy' => ['You must reference who purchased this cons family'],
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
                    'artFam_ref.required' => 'You must enter a reference for your cons family ',
                    'artFam_ref.min' => 'You must enter at least three characters ',
                    'artFam_ref.max' => 'You must enter a maximum of 255 characters',
                    'artFam_ref.String' => 'You must enter a string ',
                    'artFam_design.required' => 'You must enter a designation for your cons family ',
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

         //we checked if the reference entered is already used for another cons family
         $cons_already_exist=ConsFamily::where('consFam_ref', '=', $request->artFam_ref, 'and')->where('id', '<>', $request->artFam_id)->first() ;
         if ($cons_already_exist!=null){
             return response()->json([
                 'errors' => [
                     'artFam_ref' => ["This reference is already use for another cons family"]
                 ]
             ], 429);
         }
    }

    /**
     * Function call by ArticleFamilyForm.vue when the form is submitted for insert with the route : /cons/family/add (post)
     * Add a new enregistrement of cons family in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new cons family
     */
    public function add_consFamily(Request $request){
        $enum=NULL;
        if ($request->artFam_purchasedBy!="" && $request->artFam_purchasedBy!=NULL){
            $enum=EnumPurchasedBy::where('value', '=', $request->artFam_purchasedBy)->first() ;
            $enum=$enum->id ;
        }

        //Creation of a new consFam
        $consFamily=consFamily::create([
            'consFam_ref' => $request->artFam_ref,
            'consFam_design' => $request->artFam_design,
            'consFam_drawingPath'=> $request->artFam_drawingPath,
            'enumPurchasedBy_id' => $enum,
            'consFam_variablesCharac' => $request->artFam_variablesCharac,
            'consFam_validate' => $request->artFam_validate,
            'consFam_version' => $request->artFam_version,
            'consFam_active' => $request->artFam_active,
        ]) ;

        $consFamily_id=$consFamily->id ;

        return response()->json($consFamily_id) ;
    }

    public function send_consFamilies(){
        $consFamilies=ConsFamily::all() ;
        $array = [];
        foreach ($consFamilies as $consFamily) {
            $obj = [
                'id' => $consFamily->id,
                'consFam_ref' => $consFamily->consFam_ref,
                'consFam_design' => $consFamily->consFam_design,
                'consFam_drawingPath' => $consFamily->consFam_drawingPath,
                'consFam_variablesCharac' => $consFamily->consFam_variablesCharac,
                'consFam_version' => $consFamily->consFam_version,
                'consFam_nbrVersion' => $consFamily->compFam_nbrVersion,
                'consFam_validate' => $consFamily->consFam_validate,
                'consFam_active' => $consFamily->consFam_active
            ];
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    public function send_consFamily($id) {
        $consFamily = ConsFamily::find($id);
        $obj = [
            'id' => $consFamily->id,
            'consFam_ref' => $consFamily->consFam_ref,
            'consFam_design' => $consFamily->consFam_design,
            'consFam_drawingPath' => $consFamily->consFam_drawingPath,
            'consFam_variablesCharac' => $consFamily->consFam_variablesCharac,
            'consFam_version' => $consFamily->consFam_version,
            'consFam_nbrVersion' => $consFamily->compFam_nbrVersion,
            'consFam_validate' => $consFamily->consFam_validate,
            'consFam_active' => $consFamily->consFam_active
        ];
        return response()->json($obj);
    }
}

