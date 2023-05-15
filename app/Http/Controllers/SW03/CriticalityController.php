<?php

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\Criticality;
use App\Models\SW03\RawFamily;
use Illuminate\Http\Request;

class CriticalityController extends Controller
{
    public function verif_criticality(Request $request) {
        if ($request->crit_validate === 'validated') {
            $this->validate(
                $request,
                [
                    'crit_artCriticality' => 'required',
                    'crit_artMaterialContactCriticality' => 'required',
                    'crit_artMaterialFunctionCriticality' => 'required',
                    'crit_artProcessCriticality' => 'required',
                    'crit_remarks' => 'required|max:255',
                    'crit_articleID' => 'required',
                    'crit_articleType' => 'required',
                ],
                [
                    'crit_artCriticality.required' => 'You must enter an article criticality',

                    'crit_artMaterialContactCriticality.required' => 'You must enter an article material contact criticality',

                    'crit_artMaterialFunctionCriticality.required' => 'You must enter an article material function criticality',

                    'crit_artProcessCriticality.required' => 'You must enter an article process criticality',

                    'crit_remarks.required' => 'You must enter a remark',
                    'crit_remarks.max' => 'You must enter a maximum of 255 characters',
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
            'crit_artMaterialContactCriticality' => $request->crit_artMaterialContactCriticality,
            'crit_artMaterialFunctionCriticality' => $request->crit_artMaterialFunctionCriticality,
            'crit_artProcessCriticality' => $request->crit_artProcessCriticality,
            'crit_remarks' => $request->crit_remarks,
            'crit_validate' => $request->crit_validate,
            'consFam_id' => $consFam_id,
            'compFam_id' => $compFam_id,
            'rawFam_id' => $rawFam_id,
        ]);
    }

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
            'crit_remarks' => $request->crit_remarks,
            'crit_validate' => $request->crit_validate,
            'consFam_id' => $consFam_id,
            'compFam_id' => $compFam_id,
            'rawFam_id' => $rawFam_id,
        ]);
    }

    public function send_criticality($id) {
        $crit = Criticality::findOrfail($id);
        return response()->json([
            'id' => $crit->id,
            'crit_artCriticality' => $crit->crit_artCriticality,
            'crit_artMaterialContactCriticality' => $crit->crit_artMaterialContactCriticality,
            'crit_artMaterialFunctionCriticality' => $crit->crit_artMaterialFunctionCriticality,
            'crit_artProcessCriticality' => $crit->crit_artProcessCriticality,
            'crit_remarks' => $crit->crit_remarks,
            'crit_validate' => $crit->crit_validate,
            'consFam_id' => $crit->consFam_id,
            'compFam_id' => $crit->compFam_id,
            'rawFam_id' => $crit->rawFam_id,
        ]);
    }

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
                'crit_artMaterialContactCriticality' => $crit->crit_artMaterialContactCriticality,
                'crit_artMaterialFunctionCriticality' => $crit->crit_artMaterialFunctionCriticality,
                'crit_artProcessCriticality' => $crit->crit_artProcessCriticality,
                'crit_remarks' => $crit->crit_remarks,
                'crit_validate' => $crit->crit_validate,
                'consFam_id' => $crit->consFam_id,
                'compFam_id' => $crit->compFam_id,
                'rawFam_id' => $crit->rawFam_id,
            ]);
        }
        return response()->json($array);
    }
}
