<?php

namespace App\Http\Controllers\SW03;

use App\Http\Controllers\Controller;
use App\Models\SW03\SupplierAdr;
use Illuminate\Http\Request;

class SupplierAdrController extends Controller
{
    public function verif_adr(Request $request)
    {
        if ($request->supprAdr_validate === 'validated') {
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
                    'supplrAdr_name' => 'required|min:3|max:255'
                ],
                [
                    'supplrAdr_name.required' => 'You must enter a name',
                    'supplrAdr_name.min' => 'You must enter at least 3 characters',
                    'supplrAdr_name.max' => 'You must enter a maximum of 255 characters'
                ]
            );
        }
    }

    public function add_adr(Request $request)
    {
        $supplierAdr = SupplierAdr::create([
            'supplrAdr_name' => $request->supplr_name,
            'supplrAdr_street' => $request->supplr_street,
            'supplrAdr_town' => $request->supplr_town,
            'supplrAdr_country' => $request->supplr_country,
            'supplr_id	' => $request->supplr_id,
            'supplrAdr_validate' => $request->supplrAdr_validate,
            'supplrAdr_principal' => $request->supplrAdr_principal
        ]);
        return $supplierAdr;
    }
}
