<?php

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\Criticality;
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
        } else if ($request->crit_articleType === 'cons') {
            $consFam_id = $request->crit_articleID;
        } else if ($request->crit_articleType === 'raw') {
            $rawFam_id = $request->crit_articleID;
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
}
