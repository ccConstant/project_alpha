<?php

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\Supplier;
use App\Models\SW03\SupplierAdr;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SupplierAdrController extends Controller
{
    /**
     * @param Request $request
     * @throws ValidationException
     */
    public function verif_adr(Request $request)
    {
        print 'test';
        if ($request->supplrAdr_validate === 'validated') {
            $this->validate(
                $request,
                [
                    'supplrAdr_name' => 'required|min:3|max:255',
                    'supplrAdr_street' => 'required|min:3|max:255',
                    'supplrAdr_town' => 'required|min:3|max:255',
                    'supplrAdr_country' => 'required|min:3|max:255',
                ],
                [
                    'supplrAdr_name.required' => 'You must enter a name',
                    'supplrAdr_name.min' => 'You must enter at least 3 characters',
                    'supplrAdr_name.max' => 'You must enter a maximum of 255 characters',
                    'supplrAdr_street.required' => 'You must enter a street',
                    'supplrAdr_street.min' => 'You must enter at least 3 characters',
                    'supplrAdr_street.max' => 'You must enter a maximum of 255 characters',
                    'supplrAdr_town.required' => 'You must enter a town',
                    'supplrAdr_town.min' => 'You must enter at least 3 characters',
                    'supplrAdr_town.max' => 'You must enter a maximum of 255 characters',
                    'supplrAdr_country.required' => 'You must enter a country',
                    'supplrAdr_country.min' => 'You must enter at least 3 characters',
                    'supplrAdr_country.max' => 'You must enter a maximum of 255 characters'
                ]
            );
        } else {
            $this->validate(
                $request,
                [
                    'supplrAdr_name' => 'required|min:3|max:255',
                    'supplrAdr_town' => 'required|min:3|max:255',
                    'supplrAdr_country' => 'required|min:3|max:255',
                ],
                [
                    'supplrAdr_name.required' => 'You must enter a name',
                    'supplrAdr_name.min' => 'You must enter at least 3 characters',
                    'supplrAdr_name.max' => 'You must enter a maximum of 255 characters',
                    'supplrAdr_town.required' => 'You must enter a town',
                    'supplrAdr_town.min' => 'You must enter at least 3 characters',
                    'supplrAdr_town.max' => 'You must enter a maximum of 255 characters',
                    'supplrAdr_country.required' => 'You must enter a country',
                    'supplrAdr_country.min' => 'You must enter at least 3 characters',
                    'supplrAdr_country.max' => 'You must enter a maximum of 255 characters'
                ]
            );
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add_adr(Request $request)
    {
        $supplierAdr = SupplierAdr::create([
            'supplrAdr_name' => $request->supplrAdr_name,
            'supplrAdr_street' => $request->supplrAdr_street,
            'supplrAdr_town' => $request->supplrAdr_town,
            'supplrAdr_country' => $request->supplrAdr_country,
            'supplr_id' => $request->supplr_id,
            'supplrAdr_validate' => $request->supplrAdr_validate,
            'supplrAdr_principal' => $request->supplrAdr_principal
        ]);
        return response()->json($supplierAdr);
    }

    public function send_adr($id) {
        $supplierAdr = SupplierAdr::findOrfail($id);
        return response()->json($supplierAdr);
    }

    public function update_adr(Request $request, $id) {
        $supplierAdr = SupplierAdr::findOrfail($id);
        $supplier = Supplier::findOrfail($supplierAdr->supplr_id);
        if ($supplier->supplr_technicalReviewerId !== null) {
            $supplier->update([
                'supplr_signatureDate' => null,
                'supplr_technicalReviewerId' => null,
                'supplr_version' => $supplier->supplr_version + 1
            ]);
        }
        $supplierAdr->update([
            'supplrAdr_name' => $request->supplrAdr_name,
            'supplrAdr_street' => $request->supplrAdr_street,
            'supplrAdr_town' => $request->supplrAdr_town,
            'supplrAdr_country' => $request->supplrAdr_country,
            'supplrAdr_validate' => $request->supplrAdr_validate,
            'supplrAdr_principal' => $request->supplrAdr_principal
        ]);
        return response()->json($supplierAdr);
    }
}
