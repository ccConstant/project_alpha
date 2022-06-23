<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Mme ;  
use App\Models\MmeTemp ;  
use App\Models\MmeUsage ; 
use App\Models\EnumPrecautionType  ;
use App\Models\Precaution  ;
use Carbon\Carbon;

class PrecautionController extends Controller
{
    

     /**
     * Function call by MmePrecautionForm.vue when the form is submitted for check data with the route : /precaution/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_precaution(Request $request){              
        if ($request->prctn_validate=="VALIDATED"){
            if ($request->prctn_type=='' || $request->prctn_type==NULL ){
                return response()->json([
                    'errors' => [
                        'prctn_type' => ["You must choose a type"]
                    ]
                ], 429);
            }
        }
        
        $this->validate(
            $request,
            [
                'prctn_description' => 'required|min:3|max:255',
            ],
            [
                'prctn_description.required' => 'You must enter a description for the precaution ',
                'prctn_description.min' => 'You must enter at least three characters ',
                'prctn_description.max' => 'You must enter a maximum of 255 characters',

            
            ]
        );
        
    }

    /**
     * Function call by MmePrecautionForm.vue when the form is submitted for insert with the route :/mme/add/usage/prctn (post)
     * Add a new enregistrement of precaution in the data base with the informations entered in the form 
     * @return \Illuminate\Http\Response : the id of the new precaution
     */
    public function add_precaution(Request $request){
        $usage=MmeUsage::findOrFail($request->usage_id) ;         
    
        //A precaution is linked to its type. So we need to find the id of the type choosen by the user and write it in the attribute of the precaution.
        //But if no one type is choosen by the user we define this id to NULL
        // And if the type choosen is find in the data base the NULL value will be replace by the id value
        $type_id=NULL ; 
        if ($request->prctn_type!=''){
            $type= EnumPrecautionType::where('value', '=', $request->prctn_type)->first() ;
            $type_id=$type->id ; 
        }
        //Creation of a new precaution
        $prctn=Precaution::create([
            'prctn_description' => $request->prctn_description,
            'enumPrecautionType_id' => $type_id,
            'prctn_validate' => $request->prctn_validate,
            'mmeUsage_id' => $usage->id,
        ]) ; 
        return response()->json($prctn->id) ;
    }

     /**
     * Function call by MmePrecautionForm.vue when the form is submitted for update with the route : /mme/update/prctn (post)
     * Update an enregistrement of precaution in the data base with the informations entered in the form 
     * The id parameter correspond to the id of the equipment we want to update
     * */
    public function update_precaution(Request $request, $id){
        $precaution= Precaution::findOrFail($id) ;

        //A precaution is linked to its type. So we need to find the id of the type choosen by the user and write it in the attribute of the mme.
        //But if no one type is choosen by the user we define this id to NULL
        // And if the type choosen is find in the data base the NULL value will be replace by the id value
        $type_id=NULL ; 
        if ($request->eq_type!=''){
            $type= EnumPrecautionType::where('value', '=', $request->prctn_type)->first() ;
            $type_id=$type->id ; 
        }

        
        //Creation of a new equipment temp
        $precaution->update([
            'prctn_description' => $request->prctn_description,
            'enumPrecautionType_id' => $type_id,
            'prctn_validate' => $request->prctn_validate,
        ]);
    }


    /**
     * Function call by MmePrecautionForm when we delete a precaution with the route : /precaution/delete/{id} (post)
     * Delete a precaution 
     * The id parameter is the id of the precaution in which we want to delete
     * */

    public function delete_precaution($id){
        $precaution=Precaution::findOrFail($id) ; 
        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $id)->orderBy('created_at', 'desc')->first();
        $precaution->delete() ; 
    }

    
     /**
     * Function call by ReferenceAUsage.vue with the route : /mme/usg/send/{id} (get)
     * Get the precaution of one usage whose id is passed in parameter
     *  The id parameter corresponds to the id of the usage from which we want the precaution to take
     * @return \Illuminate\Http\Response
     */

    public function send_precautions($id) {
        $precautions=Precaution::where('mmeUsage_id', '=', $id)->latest()->first();
        $container=array() ; 
        foreach ($precaution as $precaution) {
            $type = NULL ; 
            if ($precaution->enumPrecautionType_id!=NULL){
                $type = $precaution->enumPrecautionType->value ;
            }
            $obj=([
                'id' => $precaution->id,
                'prctn_type' => $precaution->prctn_type,
                'prctn_description' => $precaution->prctn_description,
                'prctn_validate' => $precaution->prctn_validate,
            ]) ; 
            array_push($container,$obj);
        }
        return response()->json($container) ;
    }
}


