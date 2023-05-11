<?php

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;
use App\Models\SW03\ConsFamily;
use App\Models\SW03\DocumentaryControl;
use App\Models\SW03\IncomingInspection;
use App\Models\SW03\RawFamily;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DocControlController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function verif_docControl(Request $request)
    {
        if ($request->docControl_articleType === 'raw' || $request->docControl_articleType === 'comp') {
            $this->validate(
                $request,
                [
                    'docControl_materialCertifSpe' => 'required|min:2|max:255',
                    'docControl_reference' => "required|min:2|max:255",
                    'docControl_name' => "required|min:2|max:255",
                    'incmgInsp_id' => "required",
                ],
                [
                    'docControl_materialCertifSpe.required' => 'You must enter a material certificate',
                    'docControl_materialCertifSpe.min' => 'You must enter at least two characters',
                    'docControl_materialCertifSpe.max' => 'You must enter a maximum of 255 characters',

                    'docControl_reference.required' => 'You must enter a reference',
                    'docControl_reference.min' => 'You must enter at least two characters',
                    'docControl_reference.max' => 'You must enter a maximum of 255 characters',

                    'docControl_name.required' => 'You must enter a name',
                    'docControl_name.min' => 'You must enter at least two characters',
                    'docControl_name.max' => 'You must enter a maximum of 255 characters',

                    'incmgInsp_id.required' => 'You must enter an incoming inspection id'
                ]
            );
        } else if ($request->docControl_articleType === 'cons') {
            $this->validate(
                $request,
                [
                    'docControl_FDS' => 'required|min:2|max:255',
                    'docControl_reference' => "required|min:2|max:255",
                    'docControl_name' => "required|min:2|max:255",
                    'incmgInsp_id' => "required",
                ],
                [
                    'docControl_FDS.required' => 'You must enter a FDS',
                    'docControl_FDS.min' => 'You must enter at least two characters',
                    'docControl_FDS.max' => 'You must enter a maximum of 255 characters',

                    'docControl_reference.required' => 'You must enter a reference',
                    'docControl_reference.min' => 'You must enter at least two characters',
                    'docControl_reference.max' => 'You must enter a maximum of 255 characters',

                    'docControl_name.required' => 'You must enter a name',
                    'docControl_name.min' => 'You must enter at least two characters',
                    'docControl_name.max' => 'You must enter a maximum of 255 characters',

                    'incmgInsp_id.required' => 'You must enter an incoming inspection id'
                ]
            );
        }
        $insp = null;
        if ($request->docControl_articleType === 'comp') {
            $insp = IncomingInspection::all()->where('incmgInsp_compFam_id', '==', $request->article_id);
        } else if ($request->docControl_articleType === 'raw') {
            $insp = IncomingInspection::all()->where('incmgInsp_rawFam_id', '==', $request->article_id);
        } else if ($request->docControl_articleType === 'cons') {
            $insp = IncomingInspection::all()->where('incmgInsp_consFam_id', '==', $request->article_id);
        }
        $val = [];
        foreach ($insp as $in) {
            array_push($val, $in->id);
        }
        $find = DocumentaryControl::all()->where('docControl_name', '==', $request->docControl_name)
            ->whereIn('incmgInsp_id', $val)
            ->where('id', '<>', $request->id)
            ->count();
        if ($find !== 0) {
            return response()->json([
                'docControl_name' => 'This documentary control already exists',
            ], 429);
        }
    }

    public function add_docControl(Request $request) {
        $docControl = DocumentaryControl::create([
            'docControl_name' => $request->docControl_name,
            'docControl_reference' => $request->docControl_reference,
            'docControl_materialCertifSpe' => $request->docControl_materialCertifSpe,
            'incmgInsp_id' => $request->incmgInsp_id,
            'docControl_FDS' => $request->docControl_FDS,
        ]);
        return response()->json($docControl);
    }

    public function send_docControlGlob() {
        $docControl = DocumentaryControl::all();
        return response()->json($docControl);
    }

    public function send_docControlFromIncmgInsp($id) {
        $docControl = DocumentaryControl::all()->where('incmgInsp_id', "==", $id)->all();
        $array = [];
        foreach ($docControl as $doc) {
            $obj = ([
                'docControl_name' => $doc->docControl_name,
                'docControl_reference' => $doc->docControl_reference,
                'docControl_materialCertifSpe' => $doc->docControl_materialCertifSpe,
                'incmgInsp_id' => $doc->incmgInsp_id,
                'docControl_FDS' => $doc->docControl_FDS,
                'id' => $doc->id
            ]);
            array_push($array, $obj);
        }
        return response()->json($array);
    }

    public function send_docControl($id) {
        $docControl = DocumentaryControl::all()->find($id)->first();
        return response()->json([
            'docControl_name' => $docControl->docControl_name,
            'docControl_reference' => $docControl->docControl_reference,
            'docControl_materialCertifSpe' => $docControl->docControl_materialCertifSpe,
            'incmgInsp_id' => $docControl->incmgInsp_id,
            'docControl_FDS' => $docControl->docControl_FDS,
            'id' => $docControl->id
        ]);
    }

    public function update_docControl(Request $request, $id) {
        $docControl = DocumentaryControl::all()->where('id', '==', $id)->first();
        if ($docControl === null) {
            return response()->json([
                'message' => 'Documentary control not found'
            ], 404);
        };
        $incmgInsp = IncomingInspection::all()->where('id', '==', $docControl->incmgInsp_id)->first();
        if ($incmgInsp === null) {
            return response()->json([
                'message' => 'Incoming inspection not found'
            ], 404);
        };
        $article = null;
        if ($request->docControl_articleType === 'cons') {
            $article = ConsFamily::all()->where('id', '==', $incmgInsp->incmgInsp_consFam_id)->first();
            $signed = $article->consFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'consFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else if ($request->docControl_articleType === 'raw') {
            $article = RawFamily::all()->where('id', '==', $incmgInsp->incmgInsp_rawFam_id)->first();
            $signed = $article->rawFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'rawFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        } else if ($request->docControl_articleType === 'comp') {
            $article = CompFamily::all()->where('id', '==', $incmgInsp->incmgInsp_compFam_id)->first();
            $signed = $article->compFam_signatureDate;
            if ($signed !== null) {
                $article->update([
                    'compFam_nbrVersion' => $article->consFam_nbrVersion + 1,
                ]);
            }
        }
        $article->update([
            $request->docControl_articleType.'Fam_signatureDate' => null,
            $request->docControl_articleType.'Fam_qualityApproverId' => null,
            $request->docControl_articleType.'Fam_technicalReviewerId' => null,
        ]);
        $incmgInsp->update([
            'incmgInsp_qualityApproverId' => null,
            'incmgInsp_technicalReviewerId' => null,
            'incmgInsp_signatureDate' => null,
        ]);
        $docControl->update([
            'docControl_name' => $request->docControl_name,
            'docControl_reference' => $request->docControl_reference,
            'docControl_materialCertifSpe' => $request->docControl_materialCertifSpe,
            'incmgInsp_id' => $request->incmgInsp_id,
            'docControl_FDS' => $request->docControl_FDS,
        ]);
        return response()->json($docControl);
    }
}
