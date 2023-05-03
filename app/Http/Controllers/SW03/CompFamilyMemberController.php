<?php


/*
* Filename : CompFamilyMemberController.php
* Creation date : 2 May 2023
* Update date : 2 May 2023
* This file is used to link the view files and the database that concern the comp family member table.
* For example : add a comp family member in the data base, update a comp family member...
*/

namespace App\Http\Controllers\SW03;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SW03\CompFamily;
use App\Models\SW03\CompFamilyMember;
use Illuminate\Support\Facades\DB;

class CompFamilyMemberController extends Controller
{
    /**
     * Function call by ArticleFamilyMemberForm.vue when the form is submitted for check data with the route : /comp/mb/verif'(post)
     * Check the informations entered in the form and send errors if it exists
     */
    public function verif_compFamilyMember(Request $request){
        $this->validate(
            $request,
            [
                'artMb_dimension' => 'required|max:50|String',
            ],
            [

                'artMb_dimension.required' => 'You must enter a dimension for your comp family member',
                'artMb_dimension.max' => 'You must enter less than 50 characters ',
                'artMb_dimension.String' => 'You must enter a string ',
            ]
        );
    }

    /**
     * Function call by ArticleFamilyMemberForm.vue when the form is submitted for insert with the route : /comp/mb/add (post)
     * Add a new enregistrement of comp family member in the data base with the informations entered in the form
     * @return \Illuminate\Http\Response : id of the new comp family member
     */
    public function add_compFamilyMember(Request $request, $id){
        //Creation of a new compFamMember
        $compFamilyMember=compFamilyMember::create([
            'compMb_dimension' => $request->artMb_dimension,
            'compFam_id' => $id,
        ]) ;

        $compFamilyMember_id=$compFamilyMember->id ;

        return response()->json($compFamilyMember_id) ;
    }

    public function send_compFamilyMember($id) {
        $members = CompFamilyMember::all()->where('compFam_id', '==', $id);
        $array = [];
        foreach ($members as $member) {
            array_push($array, [
                'id' => $member->id,
                'dimension' => $member->compMb_dimension,
            ]);
        }
        return response()->json($array);
    }
}
