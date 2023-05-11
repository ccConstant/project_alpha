<?php

/*
* Filename : RawFamilyMemberController.php
* Creation date : 2 May 2023
* Update date : 2 May 2023
* This file is used to link the view files and the database that concern the raw family member table.
* For example : add a raw family member in the data base, update a raw family member...
*/

namespace App\Http\Controllers\SW03;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SW03\RawFamily;
use App\Models\SW03\RawFamilyMember;
use Illuminate\Support\Facades\DB;

class RawFamilyMemberController extends Controller
{
    /**
     * Function call by ArticleFamilyMemberForm.vue when the form is submitted for check data with the route : /raw/mb/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_rawFamilyMember(Request $request){

        $this->validate(
            $request,
            [
                'artMb_dimension' => 'required|max:50|String',
                'artMb_sameValues' => 'required'
            ],
            [

                'artMb_dimension.required' => 'You must enter a dimension for your raw family member',
                'artMb_dimension.max' => 'You must enter less than 50 characters ',
                'artMb_dimension.String' => 'You must enter a string ',

                'artMb_sameValues.required' => 'You must enter a boolean for your raw family member',
            ]
        );
        if (!$request->artMb_sameValues) {
            $this->validate(
                $request,
                [
                    'artMb_designation' => 'required|max:255',
                ],
                [
                    'artMb_designation.required' => 'You must enter a designation for your comp family member',
                    'artMb_designation.max' => 'You must enter less than 255 characters ',
                ]
            );
        }
    }

    /**
     * Function call by ArticleFamilyMemberForm.vue when the form is submitted for insert with the route : /raw/mb/add (post)
     * Add a new enregistrement of raw family member in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new raw family member
     */
    public function add_rawFamilyMember(Request $request, $id){
        //Creation of a new rawFamMember
        $rawFamilyMember=rawFamilyMember::create([
            'rawMb_dimension' => $request->artMb_dimension,
            'rawFam_id' => $id,
            'rawMb_sameValues' => $request->artMb_sameValues,
            'rawMb_design' => $request->artMb_designation,
        ]) ;

        $rawFamilyMember_id=$rawFamilyMember->id ;

        return response()->json($rawFamilyMember_id) ;
    }

    public function send_rawFamilyMember($id) {
        $members = RawFamilyMember::all()->where('rawFam_id', '==', $id);
        $array = [];
        foreach ($members as $member) {
            array_push($array, [
                'id' => $member->id,
                'dimension' => $member->rawMb_dimension,
                'sameValues' => $member->rawMb_sameValues,
                'designation' => $member->rawMb_design,
            ]);
        }
        return response()->json($array);
    }

    public function update_rawFamilyMember(Request $request, $id) {
        $member = RawFamilyMember::all()->where('id', '==', $id)->first();
        $member->update([
            'rawMb_dimension' => $request->artMb_dimension,
            'rawMb_sameValues' => $request->artMb_sameValues,
            'rawMb_design' => $request->artMb_designation,
        ]);
        return response()->json($member);
    }
}
