<?php

/*
* Filename : CriticalityController.php
* Creation date : 2 May 2023
* Update date : 10 Jul 2023
* This file is used to link the view files and the database that concern the criticality table.
* For example : add a criticality in the data base, update a cons family...
*/


namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;
use App\Models\SW03\CompSubFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\ConsSubFamily;
use App\Models\SW03\Criticality;
use App\Models\SW03\RawFamily;
use App\Models\SW03\RawSubFamily;
use Illuminate\Http\Request;

class CriticalityController extends Controller
{


    /**
     * Function call by CritIDForm.vue when the form is submitted for check data with the route : /artFam/criticality/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_criticality(Request $request) {
        if ($request->crit_validate === 'validated') {
            $this->validate(
                $request,
                [
                    'crit_artCriticality' => 'required',
                    'crit_performanceMedicalDevice' => 'required',
                    'crit_checkedTests' => 'required',
                    'crit_checkedTestRadioDim' => 'required',
                    'crit_checkedTestRadioFunc' => 'required',
                    'crit_checkedTestRadioAsp' => 'required',
                    'crit_checkedTestRadioDoc' => 'required',
                    'crit_checkedTestRadioAdm' => 'required',
                    'crit_remarks' => 'required|max:255',
                    'crit_articleID' => 'required',
                    'crit_articleType' => 'required',
                ],
                [
                    'crit_artCriticality.required' => 'You must inform what is the contact of the article with the patient or the user an article',

                    'crit_performanceMedicalDevice.required' => 'You must inform if the deficiencies of the article or of the material properties impact the performance of the medical device',

                    'crit_checkedTests.required' => 'You must inform if the article is subject to tests',

                    'crit_checkedTestRadioDim.required' => 'You must inform who must realize the dimensional tests',
                    'crit_checkedTestRadioFunc.required' => 'You must inform who must realize the functional tests',
                    'crit_checkedTestRadioAsp.required' => 'You must inform who must realize the aspect tests',
                    'crit_checkedTestRadioDoc.required' => 'You must inform who must realize the documentation tests',
                    'crit_checkedTestRadioAdm.required' => 'You must inform who must realize the administrative tests',

                    'crit_remarks.required' => 'You must enter a remark',
                    'crit_remarks.max' => 'You must enter a maximum of 255 characters',

                    'crit_articleID.required' => 'You must enter an article ID',

                    'crit_articleType.required' => 'You must enter an article type',

                ]
            );
        } else {
            $this->validate(
                $request,
                [
                    'crit_remarks' => 'max:255',
                ],
                [
                    'crit_remarks.max' => 'You must enter a maximum of 255 characters',
                ]
            );
        }
    }

    /**
     * Function call by CritIDForm.vue when the form is submitted for insert with the route : /artFam/criticality/add (post)
     * Add a new enregistrement of criticality in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new criticality
     */
    public function add_criticality(Request $request) {
        $compFam_id = null;
        $consFam_id = null;
        $rawFam_id = null;
        if ($request->crit_articleType === 'comp') {
            $compFam_id = $request->crit_articleID;
        } else if ($request->crit_articleType === 'cons') {
            $consFam_id = $request->crit_articleID;
        } else if ($request->crit_articleType === 'raw') {
            $rawFam_id = $request->crit_articleID;
        }
        Criticality::create([
            'crit_artCriticality' => $request->crit_artCriticality,
            'crit_performanceMedicalDevice' => $request->crit_performanceMedicalDevice,
            'crit_checkedTests' => $request->crit_checkedTests,
            'crit_checkedTestRadioDim' => $request->crit_checkedTestRadioDim,
            'crit_checkedTestRadioFunc' => $request->crit_checkedTestRadioFunc,
            'crit_checkedTestRadioAsp' => $request->crit_checkedTestRadioAsp,
            'crit_checkedTestRadioDoc' => $request->crit_checkedTestRadioDoc,
            'crit_checkedTestRadioAdm' => $request->crit_checkedTestRadioAdm,
            'crit_remarks' => $request->crit_remarks,
            'crit_validate' => $request->crit_validate,
            'consFam_id' => $consFam_id,
            'compFam_id' => $compFam_id,
            'rawFam_id' => $rawFam_id,
        ]);
    }

    /**
     * Function call by CritIDForm.vue when the form is submitted for insert with the route : /artFam/criticality/add (post)
     * Add a new enregistrement of criticality in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new criticality
     */
    public function add_criticality_subFam(Request $request) {
        $compSubFam_id = null;
        $consSubFam_id = null;
        $rawSubFam_id = null;
        if ($request->crit_articleType === 'comp') {
            $compSubFam_id = $request->crit_articleSubFamID;
        } else if ($request->crit_articleType === 'cons') {
            $consSubFam_id = $request->crit_articleSubFamID;
        } else if ($request->crit_articleType === 'raw') {
            $rawSubFam_id = $request->crit_articleSubFamID;
        }
        Criticality::create([
            'crit_artCriticality' => $request->crit_artCriticality,
            'crit_performanceMedicalDevice' => $request->crit_performanceMedicalDevice,
            'crit_checkedTests' => $request->crit_checkedTests,
            'crit_checkedTestRadioDim' => $request->crit_checkedTestRadioDim,
            'crit_checkedTestRadioFunc' => $request->crit_checkedTestRadioFunc,
            'crit_checkedTestRadioAsp' => $request->crit_checkedTestRadioAsp,
            'crit_checkedTestRadioDoc' => $request->crit_checkedTestRadioDoc,
            'crit_checkedTestRadioAdm' => $request->crit_checkedTestRadioAdm,
            'crit_remarks' => $request->crit_remarks,
            'crit_validate' => $request->crit_validate,
            'consSubFam_id' => $consSubFam_id,
            'compSubFam_id' => $compSubFam_id,
            'rawSubFam_id' => $rawSubFam_id,
        ]);
    }

     /**
     * Function call by ArticleUpdate.vue when the form is submitted for update with the route :/artFam/criticality/update/{id} (post)
     * Update an enregistrement of criticality in the data base with the informations entered in the form
     * The id parameter correspond to the id of the criticality we want to update
     * */
    public function update_criticality(Request $request, $id) {
        $crit = Criticality::findOrfail($id);
        $compFam_id = null;
        $consFam_id = null;
        $rawFam_id = null;
        if ($request->crit_articleType === 'comp') {
            $compFam_id = $request->crit_articleID;
            $compFam = CompFamily::all()->where('id', '=', $request->crit_articleID)->first();
            if ($compFam->compFam_signatureDate != null) {
                $compFam->update([
                    'compFam_nbrVersion' => $compFam->compFam_nbrVersion + 1,
                ]);
            }
            $compFam->update([
                'compFam_signatureDate' => null,
                'compFam_qualityApproverId' => null,
                'compFam_technicalReviewerId' => null,
            ]);
        } else if ($request->crit_articleType === 'cons') {
            $consFam_id = $request->crit_articleID;
            $consFam = ConsFamily::all()->where('id', '=', $request->crit_articleID)->first();
            if ($consFam->consFam_signatureDate != null) {
                $consFam->update([
                    'consFam_nbrVersion' => $consFam->consFam_nbrVersion + 1,
                ]);
            }
            $consFam->update([
                'consFam_signatureDate' => null,
                'consFam_qualityApproverId' => null,
                'consFam_technicalReviewerId' => null,
            ]);
        } else if ($request->crit_articleType === 'raw') {
            $rawFam_id = $request->crit_articleID;
            $rawFam = RawFamily::all()->where('id', '=', $request->crit_articleID)->first();
            if ($rawFam->rawFam_signatureDate != null) {
                $rawFam->update([
                    'rawFam_nbrVersion' => $rawFam->rawFam_nbrVersion + 1,
                ]);
            }
            $rawFam->update([
                'rawFam_signatureDate' => null,
                'rawFam_qualityApproverId' => null,
                'rawFam_technicalReviewerId' => null,
            ]);
        }
        $crit->update([
            'crit_artCriticality' => $request->crit_artCriticality,
            'crit_artMaterialContactCriticality' => $request->crit_artMaterialContactCriticality,
            'crit_artMaterialFunctionCriticality' => $request->crit_artMaterialFunctionCriticality,
            'crit_artProcessCriticality' => $request->crit_artProcessCriticality,
            'crit_justification' => $request->crit_justification,
            'crit_remarks' => $request->crit_remarks,
            'crit_validate' => $request->crit_validate,
            'consFam_id' => $consFam_id,
            'compFam_id' => $compFam_id,
            'rawFam_id' => $rawFam_id,
        ]);
    }


    /**
     * Function call by ArticleUpdate.vue when the form is submitted for update with the route :/artFam/criticality/update/{id} (post)
     * Update an enregistrement of criticality in the data base with the informations entered in the form
     * The id parameter correspond to the id of the criticality we want to update
     * */
    public function update_criticality_subFam(Request $request, $id) {
        $crit = Criticality::findOrfail($id);
        $subFam = null;
        $compSubFam_id = null;
        $consSubFam_id = null;
        $rawSubFam_id = null;
        if ($request->crit_articleType === 'comp') {
            $compSubFam_id = $request->crit_articleSubFamID;
            $subFam = CompSubFamily::all()->where('id', '=', $request->crit_articleSubFamID)->first();
            $compFam = CompFamily::all()->where('id', '=', $subFam->compFam_id)->first();
            if ($compFam->compFam_signatureDate != null) {
                $compFam->update([
                    'compFam_nbrVersion' => $compFam->compFam_nbrVersion + 1,
                ]);
            }
            $compFam->update([
                'compFam_signatureDate' => null,
                'compFam_qualityApproverId' => null,
                'compFam_technicalReviewerId' => null,
            ]);
        } else if ($request->crit_articleType === 'cons') {
            $consSubFam_id = $request->crit_articleSubFamID;
            $subFam = ConsSubFamily::all()->where('id', '=', $request->crit_articleSubFamID)->first();
            $consFam = ConsFamily::all()->where('id', '=', $subFam->consFam_id)->first();
            if ($consFam->consFam_signatureDate != null) {
                $consFam->update([
                    'consFam_nbrVersion' => $consFam->consFam_nbrVersion + 1,
                ]);
            }
            $consFam->update([
                'consFam_signatureDate' => null,
                'consFam_qualityApproverId' => null,
                'consFam_technicalReviewerId' => null,
            ]);
        } else if ($request->crit_articleType === 'raw') {
            $rawSubFam_id = $request->crit_articleSubFamID;
            $subFam = RawSubFamily::all()->where('id', '=', $request->crit_articleSubFamID)->first();
            $rawFam = RawFamily::all()->where('id', '=', $subFam->rawFam_id)->first();
            if ($rawFam->rawFam_signatureDate != null) {
                $rawFam->update([
                    'rawFam_nbrVersion' => $rawFam->rawFam_nbrVersion + 1,
                ]);
            }
            $rawFam->update([
                'rawFam_signatureDate' => null,
                'rawFam_qualityApproverId' => null,
                'rawFam_technicalReviewerId' => null,
            ]);
        }
        $crit->update([
            'crit_artCriticality' => $request->crit_artCriticality,
            'crit_artMaterialContactCriticality' => $request->crit_artMaterialContactCriticality,
            'crit_artMaterialFunctionCriticality' => $request->crit_artMaterialFunctionCriticality,
            'crit_artProcessCriticality' => $request->crit_artProcessCriticality,
            'crit_justification' => $request->crit_justification,
            'crit_remarks' => $request->crit_remarks,
            'crit_validate' => $request->crit_validate,
            'compSubFam_id' => $compSubFam_id,
            'consSubFam_id' => $consSubFam_id,
            'rawSubFam_id' => $rawSubFam_id,
        ]);
    }

    /**
     * Function call by ListOfArticle.vue when the form is submitted with the route : /artFam/criticality/send/{id} (post)
     * Get the criticality corresponding to the id in parameter
     * @return \Illuminate\Http\Response
     */
    public function send_criticality($id) {
        $crit = Criticality::findOrfail($id);
        return response()->json([
            'id' => $crit->id,
            'crit_artCriticality' => $crit->crit_artCriticality,
            'crit_artMaterialContactCriticality' => $crit->crit_artMaterialContactCriticality,
            'crit_artMaterialFunctionCriticality' => $crit->crit_artMaterialFunctionCriticality,
            'crit_artProcessCriticality' => $crit->crit_artProcessCriticality,
            'crit_justification' => $crit->crit_justification,
            'crit_remarks' => $crit->crit_remarks,
            'crit_validate' => $crit->crit_validate,
            'consFam_id' => $crit->consFam_id,
            'compFam_id' => $crit->compFam_id,
            'rawFam_id' => $crit->rawFam_id,
        ]);
    }

    /**
     * Function call by ListOfArticle.vue when the form is submitted with the route : /artFam/criticality/send/{id} (post)
     * Get the criticality corresponding to the id in parameter
     * @return \Illuminate\Http\Response
     */
    public function send_criticality_subFam($id) {
        $crit = Criticality::findOrfail($id);
        return response()->json([
            'id' => $crit->id,
            'crit_artCriticality' => $crit->crit_artCriticality,
            'crit_artMaterialContactCriticality' => $crit->crit_artMaterialContactCriticality,
            'crit_artMaterialFunctionCriticality' => $crit->crit_artMaterialFunctionCriticality,
            'crit_artProcessCriticality' => $crit->crit_artProcessCriticality,
            'crit_justification' => $crit->crit_justification,
            'crit_remarks' => $crit->crit_remarks,
            'crit_validate' => $crit->crit_validate,
            'consSubFam_id' => $crit->consSubFam_id,
            'compSubFam_id' => $crit->compSubFam_id,
            'rawSubFam_id' => $crit->rawSubFam_id,
        ]);
    }

    /**
     * Function call by CritIDForm.vue with the route : /artFam/criticality/send/{type}/{id}(post)
     * Get all the criticalies corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_criticalities($type, $id) {
        $array = [];
        if ($type === 'comp') {
            $crits = Criticality::all()->where('compFam_id', "==", $id);
        } else if ($type === 'cons') {
            $crits = Criticality::all()->where('consFam_id', "==", $id);
        } else if ($type === 'raw') {
            $crits = Criticality::all()->where('rawFam_id', "==", $id);
        }
        foreach ($crits as $crit) {
            array_push($array, [
                'id' => $crit->id,
                'crit_artCriticality' => $crit->crit_artCriticality,
                'crit_performanceMedicalDevice' => (boolean)$crit->crit_performanceMedicalDevice,
                'crit_checkedTests' => $crit->crit_checkedTests,
                'crit_checkedTestRadioDim' => $crit->crit_checkedTestRadioDim,
                'crit_checkedTestRadioFunc' => $crit->crit_checkedTestRadioFunc,
                'crit_checkedTestRadioAsp' => $crit->crit_checkedTestRadioAsp,
                'crit_checkedTestRadioDoc' => $crit->crit_checkedTestRadioDoc,
                'crit_checkedTestRadioAdm' => $crit->crit_checkedTestRadioAdm,
                'crit_remarks' => $crit->crit_remarks,
                'crit_validate' => $crit->crit_validate,
                'consFam_id' => $crit->consFam_id,
                'compFam_id' => $crit->compFam_id,
                'rawFam_id' => $crit->rawFam_id,
            ]);
        }
        return response()->json($array);
    }

    /**
     * Function call by CritIDForm.vue with the route : /artFam/criticality/send/{type}/{id}(post)
     * Get all the criticalies corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_criticalities_subFam($type, $id) {
        $array = [];
        if ($type === 'comp') {
            $crits = Criticality::all()->where('compSubFam_id', "==", $id);
        } else if ($type === 'cons') {
            $crits = Criticality::all()->where('consSubFam_id', "==", $id);
        } else if ($type === 'raw') {
            $crits = Criticality::all()->where('rawSubFam_id', "==", $id);
        }
        foreach ($crits as $crit) {
            array_push($array, [
                'id' => $crit->id,
                'crit_artCriticality' => $crit->crit_artCriticality,
                'crit_performanceMedicalDevice' => (boolean)$crit->crit_performanceMedicalDevice,
                'crit_checkedTests' => $crit->crit_checkedTests,
                'crit_checkedTestRadioDim' => $crit->crit_checkedTestRadioDim,
                'crit_checkedTestRadioFunc' => $crit->crit_checkedTestRadioFunc,
                'crit_checkedTestRadioAsp' => $crit->crit_checkedTestRadioAsp,
                'crit_checkedTestRadioDoc' => $crit->crit_checkedTestRadioDoc,
                'crit_checkedTestRadioAdm' => $crit->crit_checkedTestRadioAdm,
                'crit_remarks' => $crit->crit_remarks,
                'crit_validate' => $crit->crit_validate,
            ]);
        }
        return response()->json($array);
    }
}
