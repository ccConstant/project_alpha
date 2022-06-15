<?php

/*
* Filename : InformationController.php 
* Creation date : 9 Jun 2022
* Update date : 9 Jun 2022
* This file is used to link the view files and the database that concern the information table. 
* For example : update an information in the data base, send all information about ID card of equipment...
*/ 


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB ; 
use App\Models\Information ; 
use Carbon\Carbon;

class InformationController extends Controller
{

    /**
     * Function call by ??  with the route : /info/send/all (get)
     * Get all the informations (=the dictionnary) in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    /*public function send_informations_all(){
        $informations=Information::all() ; 
        return response()->json($informations) ; 
    }*/

     /**
     * Function call by InfoManagement.vue with the route : /info/send/eqIdCard (get)
     * Get all the informations (=the dictionnary) about an equipment id card in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_eqIdCard(){
        $informations=Information::where('info_set', '=', 'eqIdCard')->get() ; 
        return response()->json($informations) ; 
    }

     /**
     * Function call by InfoManagement.vue with the route : /info/send/dimension (get)
     * Get all the informations (=the dictionnary) about a dimension in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_dimension(){
        $informations=Information::where('info_set', '=', 'dimension')->get() ; 
        return response()->json($informations) ; 
    }

    /**
     * Function call by InfoManagement.vue with the route : /info/send/power (get)
     * Get all the informations (=the dictionnary) about a power in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_power(){
        $informations=Information::where('info_set', '=', 'power')->get() ; 
        return response()->json($informations) ; 
    }

     /**
     * Function call by InfoManagement.vue with the route : /info/send/specialProcess (get)
     * Get all the informations (=the dictionnary) about a special process in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_specialProcess(){
        $informations=Information::where('info_set', '=', 'specialProcess')->get() ; 
        return response()->json($informations) ; 
    }

    /**
     * Function call by InfoManagement.vue with the route : /info/send/usage (get)
     * Get all the informations (=the dictionnary) about a usage in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_usage(){
        $informations=Information::where('info_set', '=', 'usage')->get() ; 
        return response()->json($informations) ; 
    }

    /**
     * Function call by InfoManagement.vue with the route : /info/send/file (get)
     * Get all the informations (=the dictionnary) about a file in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_file(){
        $informations=Information::where('info_set', '=', 'file')->get() ; 
        return response()->json($informations) ; 
    }

    /**
     * Function call by InfoManagement.vue with the route : /info/send/preventiveMaintenanceOperation (get)
     * Get all the informations (=the dictionnary) about a preventiveMaintenanceOperation in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_preventiveMaintenanceOperation(){
        $informations=Information::where('info_set', '=', 'preventiveMaintenanceOperation')->get() ; 
        return response()->json($informations) ; 
    }

        /**
     * Function call by InfoManagement.vue with the route : /info/send/risk (get)
     * Get all the informations (=the dictionnary) about a risk in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_risk(){
        $informations=Information::where('info_set', '=', 'risk')->get() ; 
        return response()->json($informations) ; 
    }

    /**
     * Function call by InfoManagement.vue with the route : /info/send/state (get)
     * Get all the informations (=the dictionnary) about a state in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_state(){
        $informations=Information::where('info_set', '=', 'state')->get() ; 
        return response()->json($informations) ; 
    }

    /**
     * Function call by InfoManagement.vue with the route : /info/send/preventiveMaintenanceOperationRealized (get)
     * Get all the informations (=the dictionnary) about a preventiveMaintenanceOperationRealized in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_preventiveMaintenanceOperationRealized(){
        $informations=Information::where('info_set', '=', 'preventiveMaintenanceOperationRealized')->get() ; 
        return response()->json($informations) ; 
    }

    /**
     * Function call by InfoManagement.vue with the route : /info/send/curativeMaintenanceOperation (get)
     * Get all the informations (=the dictionnary) about a curativeMaintenanceOperation in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_curativeMaintenanceOperation(){
        $informations=Information::where('info_set', '=', 'curativeMaintenanceOperation')->get() ; 
        return response()->json($informations) ; 
    }

     /**
     * Function call by InfoManagement.vue with the route : /info/send/person (get)
     * Get all the informations (=the dictionnary) about a person in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_person(){
        $informations=Information::where('info_set', '=', 'person')->get() ; 
        return response()->json($informations) ; 
    }

    /**
     * Function call by InfoManagement.vue with the route : /info/send/mme (get)
     * Get all the informations (=the dictionnary) about a mme in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_mme(){
        $informations=Information::where('info_set', '=', 'mme')->get() ; 
        return response()->json($informations) ; 
    }

     /**
     * Function call by InfoManagement.vue with the route : /info/send/mme_state (get)
     * Get all the informations (=the dictionnary) about a mme state in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_mme_state(){
        $informations=Information::where('info_set', '=', 'mme_state')->get() ; 
        return response()->json($informations) ; 
    }

     /**
     * Function call by InfoManagement.vue with the route : /info/send/verif (get)
     * Get all the informations (=the dictionnary) about a verification in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_verif(){
        $informations=Information::where('info_set', '=', 'verif')->get() ; 
        return response()->json($informations) ; 
    }


     /**
     * Function call by InfoManagement.vue with the route : /info/send/verifRlz (get)
     * Get all the informations (=the dictionnary) about a verification realized in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_verifRlz(){
        $informations=Information::where('info_set', '=', 'verifRlz')->get() ; 
        return response()->json($informations) ; 
    }

      /**
     * Function call by InfoManagement.vue with the route : /info/send/mme_usage(get)
     * Get all the informations (=the dictionnary) about a mme usage in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_mme_usage(){
        $informations=Information::where('info_set', '=', 'mme_usage')->get() ; 
        return response()->json($informations) ; 
    }

      /**
     * Function call by InfoManagement.vue with the route : /info/send/mme_precaution (get)
     * Get all the informations (=the dictionnary) about a mme precaution in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_mme_precaution(){
        $informations=Information::where('info_set', '=', 'mme_precaution')->get() ; 
        return response()->json($informations) ; 
    }

        /**
     * Function call by InfoManagement.vue with the route : /info/send/enum (get)
     * Get all the informations (=the dictionnary) about the enum in the data base and send them to the vue
     * @return \Illuminate\Http\Response
     */
    public function send_informations_enum(){
       //$informations=Information::where('info_name', '=', 'enum')->get() ; 
       $informations=DB::select(DB::raw("SELECT id, info_name, info_value FROM information WHERE info_name LIKE 'enum%'"));
        return response()->json($informations) ; 
    }

     /**
     * Function call by InfoElement.vue with the route : /info/update/{id} (post)
     * Update an information in the data base thinks to the id in parameter
     * The id in parameter correspond to the id of the information we want to update
     */
    public function update_information(Request $request, $id){
        $information=Information::findOrFail($id) ; 
        //return response()->json($request->info_value) ;
        $information->update([
            'info_value' => $request->info_value,
        ]);
    }
}
