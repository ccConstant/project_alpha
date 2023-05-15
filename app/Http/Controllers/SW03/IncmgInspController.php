<?php

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\IncomingInspection;
use App\Models\SW03\RawFamily;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class IncmgInspController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function verif_incmgInsp(Request $request) {
        if ($request->incmpInsp_articleType == 'raw' || $request->incmpInsp_articleType == 'comp') {
            if ($request->incmgInsp_validate == 'validated') {
                $this->validate(
                    $request,
                    [
                        'incmgInsp_partMaterialComplianceCertificate' => 'required|max:255',
                        'incmgInsp_rawMaterialCertificate' => 'required|max:255',
                    ],
                    [
                        'incmgInsp_partMaterialComplianceCertificate.required' => 'You must enter a part material compliance certificate',
                        'incmgInsp_partMaterialComplianceCertificate.max' => 'You must enter a maximum of 255 characters',
                        'incmgInsp_rawMaterialCertificate.required' => 'You must enter a raw material certificate',
                        'incmgInsp_rawMaterialCertificate.max' => 'You must enter a maximum of 255 characters',
                    ]
                );
            }
        }
    }

    public function add_incmgInsp(Request $request) {
        $incmgInsp = IncomingInspection::create([
            'incmgInsp_remarks' => $request->incmgInsp_remarks,
            'incmgInsp_partMaterialComplianceCertificate' => $request->incmgInsp_partMaterialComplianceCertificate,
            'incmgInsp_rawMaterialCertificate' => $request->incmgInsp_rawMaterialCertificate,
            'incmgInsp_validate' => $request->incmgInsp_validate,
            'incmgInsp_consFam_id' => $request->incmgInsp_consFam_id,
            'incmgInsp_compFam_id' => $request->incmgInsp_compFam_id,
            'incmgInsp_rawFam_id' => $request->incmgInsp_rawFam_id,
        ]);
        return response()->json($incmgInsp);
    }

    public function send_incmgInspRaw($id) {
        $incmgInsps = IncomingInspection::all()->where('incmgInsp_rawFam_id', "==", $id);
        $array = [];
        foreach ($incmgInsps as $incmgInsp) {
            $obj = ([
                'id' => $incmgInsp->id,
                'incmgInsp_remarks' => $incmgInsp->incmgInsp_remarks,
                'incmgInsp_partMaterialComplianceCertificate' => $incmgInsp->incmgInsp_partMaterialComplianceCertificate,
                'incmgInsp_rawMaterialCertificate' => $incmgInsp->incmgInsp_rawMaterialCertificate,
                'incmgInsp_validate' => $incmgInsp->incmgInsp_validate,
                'incmgInsp_qualityApproverId' => $incmgInsp->incmgInsp_qualityApproverId,
                'incmgInsp_technicalReviewerId' => $incmgInsp->incmgInsp_technicalReviewerId,
                'incmgInsp_signatureDate' => $incmgInsp->incmgInsp_signatureDate,
                'incmgInsp_consFam_id' => $incmgInsp->incmgInsp_consFam_id,
                'incmgInsp_compFam_id' => $incmgInsp->incmgInsp_compFam_id,
                'incmgInsp_rawFam_id' => $incmgInsp->incmgInsp_rawFam_id,
            ]);
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    public function send_incmgInspCons($id) {
        $incmgInsps = IncomingInspection::all()->where('incmgInsp_consFam_id', "==", $id);
        $array = [];
        foreach ($incmgInsps as $incmgInsp) {
            $obj = ([
                'id' => $incmgInsp->id,
                'incmgInsp_remarks' => $incmgInsp->incmgInsp_remarks,
                'incmgInsp_partMaterialComplianceCertificate' => $incmgInsp->incmgInsp_partMaterialComplianceCertificate,
                'incmgInsp_rawMaterialCertificate' => $incmgInsp->incmgInsp_rawMaterialCertificate,
                'incmgInsp_validate' => $incmgInsp->incmgInsp_validate,
                'incmgInsp_qualityApproverId' => $incmgInsp->incmgInsp_qualityApproverId,
                'incmgInsp_technicalReviewerId' => $incmgInsp->incmgInsp_technicalReviewerId,
                'incmgInsp_signatureDate' => $incmgInsp->incmgInsp_signatureDate,
                'incmgInsp_consFam_id' => $incmgInsp->incmgInsp_consFam_id,
                'incmgInsp_compFam_id' => $incmgInsp->incmgInsp_compFam_id,
                'incmgInsp_rawFam_id' => $incmgInsp->incmgInsp_rawFam_id,
            ]);
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    public function send_incmgInspComp($id) {
        $incmgInsps = IncomingInspection::all()->where('incmgInsp_compFam_id', "==", $id);
        $array = [];
        foreach ($incmgInsps as $incmgInsp) {
            $obj = ([
                'id' => $incmgInsp->id,
                'incmgInsp_remarks' => $incmgInsp->incmgInsp_remarks,
                'incmgInsp_partMaterialComplianceCertificate' => $incmgInsp->incmgInsp_partMaterialComplianceCertificate,
                'incmgInsp_rawMaterialCertificate' => $incmgInsp->incmgInsp_rawMaterialCertificate,
                'incmgInsp_validate' => $incmgInsp->incmgInsp_validate,
                'incmgInsp_qualityApproverId' => $incmgInsp->incmgInsp_qualityApproverId,
                'incmgInsp_technicalReviewerId' => $incmgInsp->incmgInsp_technicalReviewerId,
                'incmgInsp_signatureDate' => $incmgInsp->incmgInsp_signatureDate,
                'incmgInsp_consFam_id' => $incmgInsp->incmgInsp_consFam_id,
                'incmgInsp_compFam_id' => $incmgInsp->incmgInsp_compFam_id,
                'incmgInsp_rawFam_id' => $incmgInsp->incmgInsp_rawFam_id,
            ]);
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    public function send_incmgInsps() {
        $incmgInps = IncomingInspection::all();
        return response()->json($incmgInps);
    }

    public function update_incmgInsp(Request $request, $id) {
        $incmgInsp = IncomingInspection::findOrfail($id);
        $article = null;
        if ($request->incmpInsp_articleType === 'cons') {
            $article = ConsFamily::all()->where('id', '==', $incmgInsp->incmgInsp_consFam_id)->first();
            $signed = $article->consFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'consFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else if ($request->incmpInsp_articleType === 'raw') {
            $article = RawFamily::all()->where('id', '==', $incmgInsp->incmgInsp_rawFam_id)->first();
            $signed = $article->rawFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'rawFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else if ($request->incmpInsp_articleType === 'comp') {
            $article = CompFamily::all()->where('id', '==', $incmgInsp->incmgInsp_compFam_id)->first();
            $signed = $article->compFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'compFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        }
        $article->update([
            $request->incmpInsp_articleType.'Fam_signatureDate' => null,
            $request->incmpInsp_articleType.'Fam_qualityApproverId' => null,
            $request->incmpInsp_articleType.'Fam_technicalReviewerId' => null,
        ]);
        $incmgInsp->update([
            'incmgInsp_remarks' => $request->incmgInsp_remarks,
            'incmgInsp_partMaterialComplianceCertificate' => $request->incmgInsp_partMaterialComplianceCertificate,
            'incmgInsp_rawMaterialCertificate' => $request->incmgInsp_rawMaterialCertificate,
            'incmgInsp_validate' => $request->incmgInsp_validate,
            'incmgInsp_qualityApproverId' => null,
            'incmgInsp_technicalReviewerId' => null,
            'incmgInsp_signatureDate' => null,
        ]);
        return response()->json($incmgInsp);
    }
}
