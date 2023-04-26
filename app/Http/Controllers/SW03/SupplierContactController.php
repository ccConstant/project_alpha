<?php

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\Supplier;
use App\Models\SW03\SupplierContact;
use Illuminate\Http\Request;

class SupplierContactController extends Controller
{
    public function verif_contact(Request $request) {
        if ($request->supplrContact_validate === 'validated') {
            $this->validate(
                $request,
                [
                    'supplrContact_name' => 'required|min:3|max:255',
                    'supplrContact_email' => 'required|email',
                    'supplrContact_phoneNumber' => 'required|min:3|max:255',
                    'supplrContact_function' => 'required|min:3|max:255',
                ],
                [
                    'supplrContact_name.required' => 'You must enter a name',
                    'supplrContact_name.min' => 'You must enter at least 3 characters',
                    'supplrContact_name.max' => 'You must enter a maximum of 255 characters',
                    'supplrContact_email.required' => 'You must enter an email',
                    'supplrContact_email.email' => 'You must enter a valid email',
                    'supplrContact_phoneNumber.required' => 'You must enter a phone number',
                    'supplrContact_phoneNumber.min' => 'You must enter at least 3 characters',
                    'supplrContact_phoneNumber.max' => 'You must enter a maximum of 255 characters',
                    'supplrContact_function.required' => 'You must enter a function',
                    'supplrContact_function.min' => 'You must enter at least 3 characters',
                    'supplrContact_function.max' => 'You must enter a maximum of 255 characters'
                ]
            );
        } else {
            $this->validate(
                $request,
                [
                    'supplrContact_name' => 'required|min:3|max:255'
                ],
                [
                    'supplrContact_name.required' => 'You must enter a name',
                    'supplrContact_name.min' => 'You must enter at least 3 characters',
                    'supplrContact_name.max' => 'You must enter a maximum of 255 characters'
                ]
            );
        }
    }

    public function add_contact(Request $request) {
        $supplierContact = SupplierContact::create([
            'supplrContact_name' => $request->supplrContact_name,
            'supplrContact_email' => $request->supplrContact_email,
            'supplrContact_phoneNumber' => $request->supplrContact_phoneNumber,
            'supplrContact_function' => $request->supplrContact_function,
            'supplr_id' => $request->supplr_id,
            'supplrContact_validate' => $request->supplrContact_validate,
            'supplrContact_principal' => $request->supplrContact_principal
        ]);
    }

    public function send_contact($id) {
        $supplierContact = SupplierContact::findOrfail($id);
        return response()->json($supplierContact);
    }

    public function update_contact(Request $request, $id)
    {
        $supplierContact = SupplierContact::findOrfail($id);
        $supplier = Supplier::findOrfail($supplierContact->supplr_id);
        if ($supplier->supplr_technicalReviewerId !== null) {
            $supplier->update([
                'supplr_signatureDate' => null,
                'supplr_technicalReviewerId' => null,
                'supplr_version' => $supplier->supplr_version + 1
            ]);
        }
        $supplierContact->update([
            'supplrContact_name' => $request->supplrContact_name,
            'supplrContact_email' => $request->supplrContact_email,
            'supplrContact_phoneNumber' => $request->supplrContact_phoneNumber,
            'supplrContact_function' => $request->supplrContact_function,
            'supplrContact_validate' => $request->supplrContact_validate,
            'supplrContact_principal' => $request->supplrContact_principal
        ]);
        return response()->json($supplierContact);
    }
}
