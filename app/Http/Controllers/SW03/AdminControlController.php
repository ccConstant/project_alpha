<?php

/*
* Filename : AdminControlController.php
* Creation date : 10 Jul 2023
* Update date : 11 Jul 2023
* This file is used to link the view files and the database that concern the administrative control table.
* For example : add an administrative control in the data base, update an administrative control...
*/

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\DocumentaryControl;
use App\Models\SW03\IncomingInspection;
use App\Models\SW03\RawFamily;
use Illuminate\Http\Request;
use App\Models\SW03\AdminControl;
use Illuminate\Validation\ValidationException;

class AdminControlController extends Controller
{
     /**
     * Function call by AdminControlIDForm.vue when the form is submitted for check data with the route : /incmgInsp/adminControl/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_adminControl(Request $request)
    {
        if ($request->adminControl_articleType === 'raw' || $request->adminControl_articleType === 'comp') {
            $this->validate(
                $request,
                [
                    'adminControl_materialCertifSpe' => 'required|min:2|max:255',
                    'adminControl_reference' => "required|min:2|max:255",
                    'adminControl_name' => "required|min:2|max:255",
                    'incmgInsp_id' => "required",
                ],
                [
                    'adminControl_materialCertifSpe.required' => 'You must enter a material certificate',
                    'adminControl_materialCertifSpe.min' => 'You must enter at least two characters',
                    'adminControl_materialCertifSpe.max' => 'You must enter a maximum of 255 characters',

                    'adminControl_reference.required' => 'You must enter a reference',
                    'adminControl_reference.min' => 'You must enter at least two characters',
                    'adminControl_reference.max' => 'You must enter a maximum of 255 characters',

                    'adminControl_name.required' => 'You must enter a name',
                    'adminControl_name.min' => 'You must enter at least two characters',
                    'adminControl_name.max' => 'You must enter a maximum of 255 characters',

                    'incmgInsp_id.required' => 'You must enter an incoming inspection id'
                ]
            );
        } else if ($request->adminControl_articleType === 'cons') {
            $this->validate(
                $request,
                [
                    'adminControl_FDS' => 'required|min:2|max:255',
                    'adminControl_reference' => "required|min:2|max:255",
                    'adminControl_name' => "required|min:2|max:255",
                    'incmgInsp_id' => "required",
                ],
                [
                    'adminControl_FDS.required' => 'You must enter a FDS',
                    'adminControl_FDS.min' => 'You must enter at least two characters',
                    'adminControl_FDS.max' => 'You must enter a maximum of 255 characters',

                    'adminControl_reference.required' => 'You must enter a reference',
                    'adminControl_reference.min' => 'You must enter at least two characters',
                    'adminControl_reference.max' => 'You must enter a maximum of 255 characters',

                    'adminControl_name.required' => 'You must enter a name',
                    'adminControl_name.min' => 'You must enter at least two characters',
                    'adminControl_name.max' => 'You must enter a maximum of 255 characters',

                    'incmgInsp_id.required' => 'You must enter an incoming inspection id'
                ]
            );
        }
        $insp = null;
        if ($request->adminControl_articleType === 'comp') {
            $insp = IncomingInspection::all()->where('incmgInsp_compFam_id', '==', $request->article_id);
        } else if ($request->adminControl_articleType === 'raw') {
            $insp = IncomingInspection::all()->where('incmgInsp_rawFam_id', '==', $request->article_id);
        } else if ($request->adminControl_articleType === 'cons') {
            $insp = IncomingInspection::all()->where('incmgInsp_consFam_id', '==', $request->article_id);
        }
        $val = [];
        foreach ($insp as $in) {
            array_push($val, $in->id);
        }
        $find = AdminControl::all()->where('adminControl_name', '==', $request->adminControl_name)
            ->whereIn('incmgInsp_id', $val)
            ->where('id', '<>', $request->id)
            ->count();
        if ($find !== 0) {
            return response()->json([
                'adminControl_name' => 'This documentary control already exists',
            ], 429);
        }
    }

    /**
     * Function call by AdminControlIDForm.vue when the form is submitted for insert with the route : /incmgInsp/adminControl/add (post)
     * Add a new enregistrement of a admin control in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new admin control
     */
    public function add_adminControl(Request $request) {
        $adminControl = AdminControl::create([
            'adminControl_name' => $request->adminControl_name,
            'adminControl_reference' => $request->adminControl_reference,
            'adminControl_materialCertifSpe' => $request->adminControl_materialCertifSpe,
            'incmgInsp_id' => $request->incmgInsp_id,
            'adminControl_FDS' => $request->adminControl_FDS,
        ]);
        return response()->json($adminControl);
    }

    /**
     * Function call by ReferenceAnAdminControl.vue with the route : /incmgInsp/adminControl/sendFromIncmgInsp/{id} (get)
     * Get all the doc control corresponding in the data base
     * @return \Illuminate\Http\Response
     */
    public function send_adminControlFromIncmgInsp($id) {
        $adminControl = AdminControl::all()->where('incmgInsp_id', "==", $id)->all();
        $array = [];
        foreach ($adminControl as $doc) {
            $obj = ([
                'adminControl_name' => $doc->adminControl_name,
                'adminControl_reference' => $doc->adminControl_reference,
                'adminControl_materialCertifSpe' => $doc->adminControl_materialCertifSpe,
                'incmgInsp_id' => $doc->incmgInsp_id,
                'adminControl_FDS' => $doc->adminControl_FDS,
                'id' => $doc->id
            ]);
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    /**
     * Function call by ArticleUpdate.vue when the form is submitted for update with the route :/incmgInsp/adminControl/update/{id} (post)
     * Update an enregistrement of doc control in the data base with the informations entered in the form
     * The id parameter correspond to the id of the doc control we want to update
     * */
    public function update_adminControl(Request $request, $id) {
        $adminControl = AdminControl::all()->where('id', '==', $id)->first();
        if ($adminControl === null) {
            return response()->json([
                'message' => 'Documentary control not found'
            ], 404);
        };
        $incmgInsp = IncomingInspection::all()->where('id', '==', $adminControl->incmgInsp_id)->first();
        if ($incmgInsp === null) {
            return response()->json([
                'message' => 'Incoming inspection not found'
            ], 404);
        };
        $article = null;
        if ($request->adminControl_articleType === 'cons') {
            $article = ConsFamily::all()->where('id', '==', $incmgInsp->incmgInsp_consFam_id)->first();
            $signed = $article->consFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'consFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else if ($request->adminControl_articleType === 'raw') {
            $article = RawFamily::all()->where('id', '==', $incmgInsp->incmgInsp_rawFam_id)->first();
            $signed = $article->rawFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'rawFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else if ($request->adminControl_articleType === 'comp') {
            $article = CompFamily::all()->where('id', '==', $incmgInsp->incmgInsp_compFam_id)->first();
            $signed = $article->compFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'compFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        }
        $article->update([
            $request->adminControl_articleType.'Fam_signatureDate' => null,
            $request->adminControl_articleType.'Fam_qualityApproverId' => null,
            $request->adminControl_articleType.'Fam_technicalReviewerId' => null,
        ]);
        $incmgInsp->update([
            'incmgInsp_qualityApproverId' => null,
            'incmgInsp_technicalReviewerId' => null,
            'incmgInsp_signatureDate' => null,
        ]);
        $adminControl->update([
            'adminControl_name' => $request->adminControl_name,
            'adminControl_reference' => $request->adminControl_reference,
            'adminControl_materialCertifSpe' => $request->adminControl_materialCertifSpe,
            'incmgInsp_id' => $request->incmgInsp_id,
            'adminControl_FDS' => $request->adminControl_FDS,
        ]);
        return response()->json($adminControl);
    }
}



    

   

    


