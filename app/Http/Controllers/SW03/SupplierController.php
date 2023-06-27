<?php

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SW03\Supplier;

class SupplierController extends Controller
{

    /**
     * This function is used to check if all the fields are correctly filled in the form
     * It's called through the route /supplier/verif
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function verif_supplier(Request $request)
    {
        if ($request->supplr_validate === 'validated') {
            if ($request->supplr_critical) {
                $this->validate(
                    $request,
                    [
                        'supplr_name' => 'required|min:2|max:255',
                        'supplr_receptionNumber' => 'required|min:10|max:30',
                        'supplr_formId' => 'required|max:50',
                        'supplr_specificInstructions' => 'max:255',
                        'supplr_siret' => 'required|min:2|max:255',
                        'supplr_website' => 'max:255',
                        'supplr_activity' => 'required|min:2|max:50',
                        'supplr_VATnumber' => 'required|min:2|max:255',
                        'supplr_endLinkToFolder' => 'required|min:2|max:255',
                        'supplr_agreementNumber' => 'required|min:2|max:50',
                        'supplr_qualityCertificateNumber' => 'required|min:2|max:50'
                    ],
                    [
                        'supplr_name.required' => 'You must enter a name',
                        'supplr_name.min' => 'You must enter at least two characters',
                        'supplr_name.max' => 'You must enter a maximum of 255 characters',

                        'supplr_receptionNumber.required' => 'You must enter a reception number',
                        'supplr_receptionNumber.min' => 'You must enter at least 10 characters',
                        'supplr_receptionNumber.max' => 'You must enter a maximum of 30 characters',

                        'supplr_formId.required' => 'You must enter a form ID',
                        'supplr_formId.max' => 'You must enter a maximum of 50 characters',

                        'supplr_specificInstructions.max' => 'You must enter a maximum of 255 characters',

                        'supplr_siret.required' => 'You must enter a SIRET',
                        'supplr_siret.min' => 'You must enter at least two characters',
                        'supplr_siret.max' => 'You must enter a maximum of 255 characters',

                        'supplr_website.max' => 'You must enter a maximum of 255 characters',

                        'supplr_activity.required' => 'You must enter an activity',
                        'supplr_activity.min' => 'You must enter at least two characters',
                        'supplr_activity.max' => 'You must enter a maximum of 50 characters',

                        'supplr_VATnumber.required' => 'You must enter a VAT number',
                        'supplr_VATnumber.min' => 'You must enter at least two characters',
                        'supplr_VATnumber.max' => 'You must enter a maximum of 255 characters',

                        'supplr_endLinkToFolder.required' => 'You must enter an end link to folder',
                        'supplr_endLinkToFolder.min' => 'You must enter at least two characters',
                        'supplr_endLinkToFolder.max' => 'You must enter a maximum of 255 characters',

                        'supplr_agreementNumber.required' => 'You must enter an agreement number',
                        'supplr_agreementNumber.min' => 'You must enter at least two characters',
                        'supplr_agreementNumber.max' => 'You must enter a maximum of 50 characters',

                        'supplr_qualityCertificateNumber.required' => 'You must enter a quality certification number',
                        'supplr_qualityCertificateNumber.min' => 'You must enter at least two characters',
                        'supplr_qualityCertificateNumber.max' => 'You must enter a maximum of 50 characters'
                    ]
                );
            } else {
                $this->validate(
                    $request,
                    [
                        'supplr_name' => 'required|min:2|max:255',
                        'supplr_receptionNumber' => 'required|min:10|max:30',
                        'supplr_formId' => 'required|max:50',
                        'supplr_specificInstructions' => 'max:255',
                        'supplr_siret' => 'required|min:2|max:255',
                        'supplr_website' => 'max:255',
                        'supplr_activity' => 'required|min:2|max:50',
                        'supplr_VATnumber' => 'required|min:2|max:255',
                        'supplr_endLinkToFolder' => 'required|min:2|max:255'
                    ],
                    [
                        'supplr_name.required' => 'You must enter a name',
                        'supplr_name.min' => 'You must enter at least two characters',
                        'supplr_name.max' => 'You must enter a maximum of 255 characters',

                        'supplr_receptionNumber.required' => 'You must enter a reception number',
                        'supplr_receptionNumber.min' => 'You must enter at least 10 characters',
                        'supplr_receptionNumber.max' => 'You must enter a maximum of 30 characters',

                        'supplr_formId.required' => 'You must enter a form ID',
                        'supplr_formId.max' => 'You must enter a maximum of 50 characters',

                        'supplr_specificInstructions.max' => 'You must enter a maximum of 255 characters',

                        'supplr_siret.required' => 'You must enter a SIRET',
                        'supplr_siret.min' => 'You must enter at least two characters',
                        'supplr_siret.max' => 'You must enter a maximum of 255 characters',

                        'supplr_website.max' => 'You must enter a maximum of 255 characters',

                        'supplr_activity.required' => 'You must enter an activity',
                        'supplr_activity.min' => 'You must enter at least two characters',
                        'supplr_activity.max' => 'You must enter a maximum of 50 characters',

                        'supplr_VATnumber.required' => 'You must enter a VAT number',
                        'supplr_VATnumber.min' => 'You must enter at least two characters',
                        'supplr_VATnumber.max' => 'You must enter a maximum of 255 characters',

                        'supplr_endLinkToFolder.required' => 'You must enter an end link to folder',
                        'supplr_endLinkToFolder.min' => 'You must enter at least two characters',
                        'supplr_endLinkToFolder.max' => 'You must enter a maximum of 255 characters'
                    ]
                );
            }
            if ($request->supplr_consFam_id === null &&
                $request->supplr_compFam_id === null &&
                $request->supplr_rawFam_id === null) {
                return response()->json([
                    'errors' => [
                        'supplr_consFam_id' => 'You must select one family'
                    ]
                ], 429);
            }
            // FIXME: select more than one family ?
            // FIXME: check if the vat number and the siret doesn't already exist in the database
        } else {
            $this->validate(
                $request,
                [
                    'supplr_name' => 'required|min:2|max:255',
                    'supplr_formId' => 'required|max:50',
                ],
                [
                    'supplr_name.required' => 'You must enter a name',
                    'supplr_name.min' => 'You must enter at least two characters',
                    'supplr_name.max' => 'You must enter a maximum of 255 characters',
                    'supplr_formId.required' => 'You must enter a form ID',
                    'supplr_formId.max' => 'You must enter a maximum of 50 characters',
                ]
            );
        }
    }

    public function add_supplier(Request $request) {
        $supplier = Supplier::create([
            'supplr_name' => $request->supplr_name,
            'supplr_receptionNumber' => $request->supplr_receptionNumber,
            'supplr_formID' => $request->supplr_formId,
            'supplr_consFam_id' => $request->supplr_consFam_id,
            'supplr_compFam_id' => $request->supplr_compFam_id,
            'supplr_rawFam_id' => $request->supplr_rawFam_id,
            'supplr_agreementNumber' => $request->supplr_agreementNumber,
            'supplr_qualityCertificationNumber' => $request->supplr_qualityCertificateNumber,
            'supplr_specificInstructions' => $request->supplr_specificInstructions,
            'supplr_version' => 1,
            'supplr_validate' => $request->supplr_validate,
            'supplr_siret' => $request->supplr_siret,
            'supplr_website' => $request->supplr_website,
            'supplr_activity' => $request->supplr_activity,
            'supplr_real' => $request->supplr_real,
            'supplr_VATNumber' => $request->supplr_VATnumber,
            'supplr_critical' => $request->supplr_critical,
            'supplr_endLinkToFolder' => $request->supplr_endLinkToFolder,
            'supplr_active' => $request->supplr_active,
        ]);
        return response()->json($supplier);
    }

    public function send_supplier($id) {
        $supplier = Supplier::findOrfail($id);
        return response()->json([
            'supplr_name' => $supplier->supplr_name,
            'supplr_receptionNumber' => $supplier->supplr_receptionNumber,
            'supplr_formId' => $supplier->supplr_formID,
            'supplr_consFam_id' => $supplier->supplr_consFam_id,
            'supplr_compFam_id' => $supplier->supplr_compFam_id,
            'supplr_rawFam_id' => $supplier->supplr_rawFam_id,
            'supplr_agreementNumber' => $supplier->supplr_agreementNumber,
            'supplr_qualityCertificationNumber' => $supplier->supplr_qualityCertificationNumber,
            'supplr_specificInstructions' => $supplier->supplr_specificInstructions,
            'supplr_version' => $supplier->supplr_version,
            'supplr_validate' => $supplier->supplr_validate,
            'supplr_siret' => $supplier->supplr_siret,
            'supplr_website' => $supplier->supplr_website,
            'supplr_activity' => $supplier->supplr_activity,
            'supplr_real' => (boolean)$supplier->supplr_real,
            'supplr_VATNumber' => $supplier->supplr_VATNumber,
            'supplr_critical' => (boolean)$supplier->supplr_critical,
            'supplr_endLinkToFolder' => $supplier->supplr_endLinkToFolder,
            'supplr_active' => (boolean)$supplier->supplr_active,
            'supplr_technicalReviewerId' => $supplier->supplr_technicalReviewerId,
            'supplr_signatureDate' => $supplier->supplr_signatureDate,
            'supplr_created_at' => $supplier->supplr_created_at,
            'supplr_updated_at' => $supplier->supplr_updated_at,
            'supplr_id' => $supplier->id,
        ]);
    }

    public function send_suppliers() {
        $suppliers = Supplier::all()->orderBy('supplr_name', 'asc');
        return response()->json($suppliers);
    }

    public function send_active_suppliers() {
        $suppliers = Supplier::all()->where('supplr_active', '=', 1);
        return response()->json($suppliers);
    }

    public function update_supplier(Request $request, $id) {
        $supplier = Supplier::findOrfail($id);
        if ($supplier->supplr_technicalReviewerId !== null) {
            $supplier->update([
                'supplr_signatureDate' => null,
                'supplr_technicalReviewerId' => null,
                'supplr_version' => $supplier->supplr_version + 1
            ]);
        }
        $supplier->update([
            'supplr_name' => $request->supplr_name,
            'supplr_receptionNumber' => $request->supplr_receptionNumber,
            'supplr_formId' => $request->supplr_formId,
            'supplr_agreementNumber' => $request->supplr_agreementNumber,
            'supplr_qualityCertificateNumber' => $request->supplr_qualityCertificateNumber,
            'supplr_specificInstructions' => $request->supplr_specificInstructions,
            'supplr_siret' => $request->supplr_siret,
            'supplr_website' => $request->supplr_website,
            'supplr_activity' => $request->supplr_activity,
            'supplr_VATnumber' => $request->supplr_VATnumber,
            'supplr_endLinkToFolder' => $request->supplr_endLinkToFolder,
            'supplr_consFam_id' => $request->supplr_consFam_id,
            'supplr_compFam_id' => $request->supplr_compFam_id,
            'supplr_rawFam_id' => $request->supplr_rawFam_id,
            'supplr_validate' => $request->supplr_validate
        ]);
        return response()->json($supplier);
    }
}
