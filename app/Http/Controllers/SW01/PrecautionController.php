<?php

namespace App\Http\Controllers\SW01;


use Illuminate\Http\Request;
use App\Models\SW01\Mme ;  
use App\Models\SW01\MmeTemp ;  
use App\Models\SW01\MmeUsage ; 
use App\Models\SW01\EnumPrecautionType  ;
use App\Models\SW01\Precaution  ;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class PrecautionController extends Controller
{
    

     /**
     * Function call by MmePrecautionForm.vue when the form is submitted for check data with the route : /precaution/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_precaution(Request $request){              
        if ($request->prctn_validate=="validated"){
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

        $mme=Mme::findOrfail($request->mme_id) ; 
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $request->mme_id)->orderBy('created_at', 'desc')->first();

        if ($mostRecentlyMmeTmp->qualityVerifier_id!=null){
            $mostRecentlyMmeTmp->update([
                'qualityVerifier_id' => NULL,
            ]);
        }
        if ($mostRecentlyMmeTmp->technicalVerifier_id!=null){
            $mostRecentlyMmeTmp->update([
                'technicalVerifier_id' => NULL,
            ]);
        }

        //Creation of a new precaution
        $prctn=Precaution::create([
            'prctn_description' => $request->prctn_description,
            'enumPrecautionType_id' => $type_id,
            'prctn_validate' => $request->prctn_validate,
            'mmeUsage_id' => $usage->id,
        ]) ; 


        //If the mme temp is validated and a life sheet has been already created, we need to update the mme temp and increase it's version (that's mean another life sheet version) for add precaution
        if ((boolean)$mostRecentlyMmeTmp->mmeTemp_lifeSheetCreated==true && $mostRecentlyMmeTmp->mmeTemp_validate=="validated"){
                
            //We need to increase the number of mme temp linked to the mme
            $version_mme=$mme->mme_nbrVersion+1 ; 
            //Update of mme
            $mme->update([
                'mme_nbrVersion' =>$version_mme,
            ]);
            
            //We need to increase the version of the mme temp (because we create a new mme temp)
            $version =  $mostRecentlyMmeTmp->mmeTemp_version+1 ; 
            //update of mme temp
            $mostRecentlyMmeTmp->update([
             'mmeTemp_version' => $version,
             'mmeTemp_date' => Carbon::now('Europe/Paris'),
             'mmeTemp_lifeSheetCreated' => false,
            ]);
        }
        return response()->json($prctn->id) ;
    }

    /**
     * Function call by LifeSheetPDF.vue with the route : /prctn/send/pdf/{$id} (get)
     * Get all the prctn of all the usages linked of one mme whose id is passed in parameter
     * The id parameter corresponds to the id of the mme from which we want the prctn linked to the usage linked 
     * @return \Illuminate\Http\Response
     */

    public function send_precautions_pdf($id) {
        $usages=MmeUsage::where('mmeTemp_id', '=', $id)->get() ; 
        $container=array(); 
        foreach($usages as $usage){
            $prctns = Precaution::where('mmeUsage_id', '=', $usage->id)->get();
            foreach ($prctns as $prctn) {
                $obj=([
                    'id' => $prctn->id,
                    'prctn_description' => $prctn->prctn_description,
                    'usg_id' => $usage->id,
                ]) ; 
                array_push($container,$obj);
            }
        }
        return response()->json($container) ;
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
        if ($request->prctn_type!=''){
            $type= EnumPrecautionType::where('value', '=', $request->prctn_type)->first() ;
            $type_id=$type->id ; 
        }

        $mme=Mme::findOrfail($request->mme_id) ; 
        //We search the most recently mme temp of the mme 
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $request->mme_id)->latest()->first();
        if ($mostRecentlyMmeTmp!=NULL){

            if ($mostRecentlyMmeTmp->qualityVerifier_id!=null){
                $mostRecentlyMmeTmp->update([
                    'qualityVerifier_id' => NULL,
                ]);
            }
            if ($mostRecentlyMmeTmp->technicalVerifier_id!=null){
                $mostRecentlyMmeTmp->update([
                    'technicalVerifier_id' => NULL,
                ]);
            }

            //We checked if the most recently mme temp is validate and if a life sheet has been already created.
            //If the mme temp is validated and a life sheet has been already created, we need to update the mme temp and increase it's version (that's mean another life sheet version) for add usage
            if ($mostRecentlyMmeTmp->mmeTemp_validate=="validated" && (boolean)$mostRecentlyMmeTmp->mmeTemp_lifeSheetCreated==true){
            
                //We need to increase the number of mme temp linked to the mme
                $version_mme=$mme->mme_nbrVersion+1 ; 
                //Update of mme
                $mme->update([
                    'mme_nbrVersion' =>$version_mme,
                ]);

                //We need to increase the version of the mme temp (because we create a new mme temp)
               $version =  $mostRecentlyMmeTmp->mmeTemp_version+1 ; 
               //update of mme temp
               $mostRecentlyMmeTmp->update([
                'mmeTemp_version' => $version,
                'mmeTemp_date' => Carbon::now('Europe/Paris'),
                'mmeTemp_lifeSheetCreated' => false,
               ]);
                
                // In the other case, we can modify the informations without problems
            }
            $precaution->update([
                'prctn_description' => $request->prctn_description,
                'enumPrecautionType_id' => $type_id,
                'prctn_validate' => $request->prctn_validate,
            ]);
            
        }
    }


    /**
     * Function call by MmePrecautionForm when we delete a precaution with the route : /precaution/delete/{id} (post)
     * Delete a precaution 
     * The id parameter is the id of the precaution in which we want to delete
     * */

    public function delete_precaution(Request $request, $id){
        $mme=Mme::findOrfail($request->mme_id) ; 
        //We search the most recently mme temp of the mme 
        $mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $request->mme_id)->latest()->first();
        
        if ($mostRecentlyMmeTmp->qualityVerifier_id!=null){
            $mostRecentlyMmeTmp->update([
                'qualityVerifier_id' => NULL,
            ]);
        }
        if ($mostRecentlyMmeTmp->technicalVerifier_id!=null){
            $mostRecentlyMmeTmp->update([
                'technicalVerifier_id' => NULL,
            ]);
        }
        
        //We checked if the most recently mme temp is validate and if a life sheet has been already created.
        //If the mme temp is validated and a life sheet has been already created, we need to update the mme temp and increase it's version (that's mean another life sheet version) for update dimension
        if ($mostRecentlyMmeTmp->mmeTemp_validate=="validated" && (boolean)$mostRecentlyMmeTmp->mmeTemp_lifeSheetCreated==true){
            //We need to increase the number of mme temp linked to the mme
            $version_mme=$mme->mme_nbrVersion+1 ; 
            //Update of mme
            $mme->update([
                'mme_nbrVersion' =>$version_mme,
            ]);

            //We need to increase the version of the mme temp (because we create a new mme temp)
            $version =  $mostRecentlyMmeTmp->mmeTemp_version+1 ; 
            //update of mme temp
            $mostRecentlyMmeTmp->update([
            'mmeTemp_version' => $version,
            'mmeTemp_date' => Carbon::now('Europe/Paris'),
            'mmeTemp_lifeSheetCreated' => false,
            ]);
        }
        $precaution=Precaution::findOrFail($id) ; 
        $precaution->delete() ; 
    }

    
     /**
     * Function call by MmeUsageForm.vue with the route : /mme/usg/send/{id} (get)
     * Get the precaution of one usage whose id is passed in parameter
     *  The id parameter corresponds to the id of the usage from which we want the precaution to take
     * @return \Illuminate\Http\Response
     */

    public function send_precautions($id) {
        $precautions=Precaution::where('mmeUsage_id', '=', $id)->get();
        $container=array() ; 
        foreach ($precautions as $precaution) {
            $type = NULL ; 
            if ($precaution->enumPrecautionType_id!=NULL){
                $type = $precaution->enumPrecautionType->value ;
            }
            $obj=([
                'id' => $precaution->id,
                'prctn_type' => $type,
                'prctn_description' => $precaution->prctn_description,
                'prctn_validate' => $precaution->prctn_validate,
            ]) ; 
            array_push($container,$obj);
        }
        return response()->json($container) ;
    }
}


