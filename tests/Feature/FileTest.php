<?php

/**
 * Filename: FileTest.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file contains all the tests about the file table.
 * Coverage : 100%
 */

namespace Tests\Feature;

use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\File;
use App\Models\SW01\Mme;
use App\Models\SW01\MmeTemp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    public function make_an_equipment($name, $validate)
    {
        if ($validate != "validated") {
            $countEquipment = Equipment::all()->count();
            $response = $this->post('equipment/verif', [
                'eq_validate' => $validate,
                'eq_internalReference' => $name,
                'eq_externalReference' => $name,
                'eq_location' => $name,
            ]);
            $response->assertStatus(200);
            $response->assertSessionHasNoErrors();
            $response = $this->post('/equipment/add', [
                'eq_validate' => $validate,
                'eq_internalReference' => $name,
                'eq_externalReference' => $name,
                'eq_name' => $name,
            ]);
            $response->assertStatus(200);
            $this->assertEquals($countEquipment+1, Equipment::all()->count());
            return Equipment::all()->where('eq_internalReference', '==',$name)->first();
        }
        $response=$this->post('/equipment/enum/massUnit/add', [
            'value' => 'g'
        ]);
        if ($response->getStatusCode() === 200) {
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_massUnit' => 'The value of the field for the new equipment mass unit already exist in the data base'
            ]);
        }
        $response=$this->post('/equipment/enum/type/add', [
            'value' => 'internal'
        ]);
        if ($response->getStatusCode() === 200) {
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_type' => 'The value of the field for the new equipment type already exist in the data base'
            ]);
        }
        $countEquipment = Equipment::all()->count();
        $response = $this->post('equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => $name,
            'eq_externalReference' => $name,
            'eq_name' => $name,
            'eq_serialNumber' => $name,
            'eq_constructor' => $name,
            'eq_set' => $name,
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => $name,
            'eq_mobility' => true,
            'eq_type' => 'internal',
            'eq_location' => $name,
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => $name,
            'eq_externalReference' => $name,
            'eq_name' => $name,
            'eq_serialNumber' => $name,
            'eq_constructor' => $name,
            'eq_set' => $name,
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => $name,
            'eq_mobility' => true,
            'eq_type' => 'internal',
            'eq_location' => $name,
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $equipment = Equipment::all()->where('eq_internalReference', '==',$name)->first();
        $this->make_an_eq_verif($equipment->id);
        return $equipment;
    }

    public function make_an_eq_verif($eq_id)
    {
        $user = $this->make_a_user();
        $response=$this->post('/equipment/validation/'.$eq_id, [
            'reason' => 'technical',
            'enteredBy_id' => $user->id,
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/validation/'.$eq_id, [
            'reason' => 'quality',
            'enteredBy_id' => $user->id,
        ]);
        $response->assertStatus(200);
        $this->assertEquals($user->id, EquipmentTemp::all()->where('equipment_id', '==', $eq_id)->last()->qualityVerifier_id);
        $this->assertEquals($user->id, EquipmentTemp::all()->where('equipment_id', '==', $eq_id)->last()->technicalVerifier_id);
    }

    public function make_a_mme($name, $validate)
    {
        if ($validate != "validated") {
            $countMme = Mme::all()->count();
            $response = $this->post('mme/verif', [
                'mme_validate' => $validate,
                'mme_internalReference' => $name,
                'mme_externalReference' => $name
            ]);
            $response->assertStatus(200);
            $response->assertSessionHasNoErrors();
            $response = $this->post('/mme/add', [
                'mme_validate' => $validate,
                'mme_internalReference' => $name,
                'mme_externalReference' => $name
            ]);
            $response->assertStatus(200);
            $this->assertEquals($countMme+1, Mme::all()->count());
            return Mme::all()->where('mme_internalReference', '==',$name)->first();
        }
        $countMme = Mme::all()->count();
        $response = $this->post('mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => $name,
            'mme_externalReference' => $name,
            'mme_name' => $name,
            'mme_serialNumber' => $name,
            'mme_constructor' => $name,
            'mme_remarks' => $name,
            'mme_set' => $name,
            'mme_location' => $name,
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response = $this->post('/mme/add', [
            'mme_validate' => 'validated',
            'mme_internalReference' => $name,
            'mme_externalReference' => $name,
            'mme_name' => $name,
            'mme_serialNumber' => $name,
            'mme_constructor' => $name,
            'mme_remarks' => $name,
            'mme_set' => $name,
            'mme_location' => $name,
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countMme+1, Mme::all()->count());
        $Mme = Mme::all()->where('mme_internalReference', '==',$name)->first();
        $this->make_a_mme_verif($Mme->id);
        return $Mme;
    }

    public function make_a_mme_verif($mme_id)
    {
        $user = $this->make_a_user();
        $response=$this->post('/mme/validation/'.$mme_id, [
            'reason' => 'technical',
            'enteredBy_id' => $user->id,
        ]);
        $response->assertStatus(200);
        $response=$this->post('/mme/validation/'.$mme_id, [
            'reason' => 'quality',
            'enteredBy_id' => $user->id,
        ]);
        $response->assertStatus(200);
        $this->assertEquals($user->id, MmeTemp::all()->where('mme_id', '==', $mme_id)->last()->qualityVerifier_id);
        $this->assertEquals($user->id, MmeTemp::all()->where('mme_id', '==', $mme_id)->last()->technicalVerifier_id);
    }

    public function make_a_user()
    {
        $countUser=User::all()->count();
        $response=$this->post('register', [
            'user_firstName' => 'VerifierVerifier',
            'user_lastName' => 'VerifierVerifier',
            'user_pseudo' => 'VerifierVerifier',
            'user_password' => 'VerifierVerifier',
            'user_confirmation_password' => 'VerifierVerifier',
        ]);
        //if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
            $this->assertEquals($countUser+1, User::all()->count());
        //} else {
          //  $response->assertStatus(429);
            //$response->assertInvalid([
              //  'user_pseudo' => 'This username is already used'
            //]);
        //}
        return User::all()->where('user_pseudo', '==', 'Verifier')->first();
    }

    /**
     * Test Conception Number: 1
     * Saved a file as drafted from add menu with no values
     * File name: /
     * File Location: /
     * Expected result: Receiving an error:
     *                                      "You must enter a name for your file"
     * @returns void
     */
    public function test_add_file_drafted_no_value()
    {
        $response = $this->post('file/verif', [
            'file_validate' => 'drafted'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'file_name' => 'You must enter a name for your file'
        ]);
    }

    /**
     * Test Conception Number: 2
     * Saved a file as drafted from add menu with a too short name
     * File name: "in"
     * File Location: /
     * Expected result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     * @returns void
    */
    public function test_add_file_drafted_too_short_name()
    {
        $response = $this->post('file/verif', [
            'file_validate' => 'drafted',
            'file_name' => 'in'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'file_name' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 3
     * Saved a file as drafted with a too long name
     * File name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * File Location: /
     * Expected result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
    */
    public function test_add_file_drafted_too_long_name()
    {
        $response = $this->post('file/verif', [
            'file_validate' => 'drafted',
            'file_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'file_name' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 4
     * Saved a file as to be validated from add menu with no values
     * File name: /
     * File Location: /
     * Expected result: Receiving an error:
     *                                      "You must enter a name for your file"
     * @returns void
    */
    public function test_add_file_to_be_validated_no_value()
    {
        $response = $this->post('file/verif', [
            'file_validate' => 'to_be_validated'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'file_name' => 'You must enter a name for your file'
        ]);
    }

    /**
     * Test Conception Number: 5
     * Saved a file as to be validated from add menu with a too short name
     * File name: "in"
     * File Location: /
     * Expected result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     * @returns void
    */
    public function test_add_file_to_be_validated_too_short_name()
    {
        $response = $this->post('file/verif', [
            'file_validate' => 'to_be_validated',
            'file_name' => 'in'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'file_name' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 6
     * Saved a file as to be validated with a too long name
     * File name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * File Location: /
     * Expected result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
    */
    public function test_add_file_to_be_validated_too_long_name()
    {
        $response = $this->post('file/verif', [
            'file_validate' => 'to_be_validated',
            'file_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'file_name' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 7
     * Saved a file as validated from add menu without values
     * File name: /
     * File Location: /
     * Expected result: Receiving an error:
     *                                      "You must enter a name for your file"
     *                                      "You must enter a location for your file"
     * @returns void
    */
    public function test_add_file_validated_no_value()
    {
        $response = $this->post('file/verif', [
            'file_validate' => 'validated'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'file_name' => 'You must enter a name for your file',
            'file_location' => 'You must enter a location for your file'
        ]);
    }

    /**
     * Test Conception Number: 8
     * Saved a file as validated from add menu with a too short name
     * File name: "in"
     * File Location: /
     * Expected result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     *                                      "You must enter a location for your file"
     * @returns void
     */
    public function test_add_file_validated_too_short_name()
    {
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'in'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'file_name' => 'You must enter at least three characters',
            'file_location' => 'You must enter a location for your file'
        ]);
    }

    /**
     * Test Conception Number: 9
     * Saved a file as validated with a too long name
     * File name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * File Location: /
     * Expected result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     *                                      "You must enter a location for your file"
     * @returns void
     */
    public function test_add_file_validated_too_long_name()
    {
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'file_name' => 'You must enter a maximum of 50 characters',
            'file_location' => 'You must enter a location for your file'
        ]);
    }

    /**
     * Test Conception Number: 10
     * Saved a file as validated with a too short location
     * File name: "File"
     * File Location: "in"
     * Expected result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     * @returns void
     */
    public function test_add_file_validated_too_short_location()
    {
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'file_location' => 'in'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'file_location' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 11
     * Successfully save as drafted from add menu a file linked to an equipment
     * File name: "File"
     * File Location: /
     * Expected result: The file is correctly added in the database
     * @returns void
     */
    public function test_add_file_draft_success()
    {
        $equipment = $this->make_an_equipment('addDraftEqTest', 'drafted');
        // Add a file to the equipment
        $response = $this->post('file/verif', [
            'file_validate' => 'drafted',
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $countFile = File::all()->count();
        $this->post('/equipment/add/file/', [
            'file_validate' => 'drafted',
            'eq_id' => $equipment->id,
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countFile+1, File::all()->count());
        $this->assertDatabaseHas('files', [
            'file_name' => 'File',
            'file_validate' => 'drafted',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id
        ]);
    }

    /**
     * Test Conception Number: 12
     * Successfully save as drafted from add menu a file linked to a mme
     * File name: "File"
     * File Location: /
     * Expected result: The file is correctly added in the database
     * @returns void
    */
    public function test_add_file_draft_success_mme()
    {
        $mme = $this->make_a_mme('addDraftMmeTest', 'drafted');
        // Add a file to the mme
        $response = $this->post('file/verif', [
            'file_validate' => 'drafted',
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $countFile = File::all()->count();
        $this->post('/mme/add/file/', [
            'file_validate' => 'drafted',
            'mme_id' => $mme->id,
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countFile+1, File::all()->count());
        $this->assertDatabaseHas('files', [
            'file_name' => 'File',
            'file_validate' => 'drafted',
            'mmeTemp_id' => $mme->id
        ]);
    }

    /**
     * Test Conception Number: 13
     * Successfully save as to be validated from add menu a file linked to an equipment
     * File name: "File"
     * File Location: /
     * Expected result: The file is correctly added in the database
     * @returns void
    */
    public function test_add_file_to_be_validated_success()
    {
        $equipment = $this->make_an_equipment('addTbvEqTest', 'drafted');
        // Add a file to the equipment
        $response = $this->post('file/verif', [
            'file_validate' => 'to_be_validated',
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $countFile = File::all()->count();
        $this->post('/equipment/add/file/', [
            'file_validate' => 'to_be_validated',
            'eq_id' => $equipment->id,
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countFile+1, File::all()->count());
        $this->assertDatabaseHas('files', [
            'file_name' => 'File',
            'file_validate' => 'to_be_validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id
        ]);
    }

    /**
     * Test Conception Number: 14
     * Successfully save as to be validated from add menu a file linked to a mme
     * File name: "File"
     * File Location: /
     * Expected result: The file is correctly added in the database
     * @returns void
    */
    public function test_add_file_to_be_validated_success_mme()
    {
        $mme = $this->make_a_mme('addTbvMmeTest', 'drafted');
        // Add a file to the mme
        $response = $this->post('file/verif', [
            'file_validate' => 'to_be_validated',
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $countFile = File::all()->count();
        $this->post('/mme/add/file/', [
            'file_validate' => 'to_be_validated',
            'mme_id' => $mme->id,
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countFile+1, File::all()->count());
        $this->assertDatabaseHas('files', [
            'file_name' => 'File',
            'file_validate' => 'to_be_validated',
            'mmeTemp_id' => $mme->id
        ]);
    }

    /*
     * Test Conception Number: 15
     * Successfully save as validated from add menu a file linked to an equipment
     * File name: "File"
     * File Location: "FilePath"
     * Expected result: The file is correctly added in the database
     * @returns void
    */
    public function test_add_file_validated_success()
    {
        $equipment = $this->make_an_equipment('addValidEqTest', 'drafted');
        // Add a file to the equipment
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'file_location' => 'FilePath'
        ]);
        $response->assertStatus(200);
        $countFile = File::all()->count();
        $this->post('/equipment/add/file/', [
            'file_validate' => 'validated',
            'eq_id' => $equipment->id,
            'file_name' => 'File',
            'file_location' => 'FilePath'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countFile+1, File::all()->count());
        $this->assertDatabaseHas('files', [
            'file_name' => 'File',
            'file_validate' => 'validated',
            'file_location' => 'FilePath',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id
        ]);
    }

    /**
     * Test Conception Number: 16
     * Successfully save as validated from add menu a file linked to a mme
     * File name: "File"
     * File Location: "FilePath"
     * Expected result: The file is correctly added in the database
     * @returns void
     */
    public function test_add_file_validated_success_mme()
    {
        $mme = $this->make_a_mme('addValidMmeTest', 'drafted');
        // Add a file to the mme
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'file_location' => 'FilePath'
        ]);
        $response->assertStatus(200);
        $countFile = File::all()->count();
        $this->post('/mme/add/file/', [
            'file_validate' => 'validated',
            'mme_id' => $mme->id,
            'file_name' => 'File',
            'file_location' => 'FilePath'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countFile+1, File::all()->count());
        $this->assertDatabaseHas('files', [
            'file_name' => 'File',
            'file_validate' => 'validated',
            'file_location' => 'FilePath',
            'mmeTemp_id' => $mme->id
        ]);
    }

    /**
     * Test Conception Number: 17
     * Successfully save as drafted from add menu a file linked to a validated equipment
     * File name: "File"
     * File location: /
     * Expected result: The file is correctly added in the database and the version of the equipment is changed
     * @returns void
    */
    public function test_add_file_drafted_success_validated_eq()
    {
        $equipment = $this->make_an_equipment('addDraftEqVTest', 'validated');
        $actualVersion = EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addDraftEqVTest')->last()->eqTemp_version;
        // Add a file to the equipment
        $response = $this->post('file/verif', [
            'file_validate' => 'drafted',
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $countFile = File::all()->count();
        $this->post('/equipment/add/file/', [
            'file_validate' => 'drafted',
            'eq_id' => $equipment->id,
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countFile+1, File::all()->count());
        $this->assertDatabaseHas('files', [
            'file_name' => 'File',
            'file_validate' => 'drafted',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id
        ]);
        $this->assertEquals($actualVersion+1, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addDraftEqVTest')->last()->eqTemp_version);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addDraftEqVTest')->last()->technicalVerifier_id);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addDraftEqVTest')->last()->qualityVerifier_id);
        $this->assertEquals(0, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addDraftEqVTest')->last()->eqTemp_lifeSheetCreated);
    }

    /**
     * Test Conception Number: 18
     * Successfully save as drafted from add menu a file linked to a validated mme
     * File name: "File"
     * File location: /
     * Expected result: The file is correctly added in the database and the version of the mme is changed
     * @return void
    */
    public function test_add_file_drafted_success_validated_mme()
    {
        $Mme = $this->make_a_mme('addDraftMmeVTest', 'validated');
        $actualVersion = MmeTemp::all()->where('mmeTemp_remarks', '==', 'addDraftMmeVTest')->last()->mmeTemp_version;
        // Add a file to the equipment
        $response = $this->post('file/verif', [
            'file_validate' => 'drafted',
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $countFile = File::all()->count();
        $this->post('/mme/add/file/', [
            'file_validate' => 'drafted',
            'mme_id' => $Mme->id,
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countFile+1, File::all()->count());
        $this->assertDatabaseHas('files', [
            'file_name' => 'File',
            'file_validate' => 'drafted',
            'mmeTemp_id' => $Mme->id
        ]);
        $this->assertEquals($actualVersion+1, MmeTemp::all()->where('mmeTemp_remarks', '==', 'addDraftMmeVTest')->last()->mmeTemp_version);
        $this->assertNull(MmeTemp::all()->where('mmeTemp_remarks', '==', 'addDraftMmeVTest')->last()->technicalVerifier_id);
        $this->assertNull(MmeTemp::all()->where('mmeTemp_remarks', '==', 'addDraftMmeVTest')->last()->qualityVerifier_id);
        $this->assertEquals(0, MmeTemp::all()->where('mmeTemp_remarks', '==', 'addDraftMmeVTest')->last()->mmeTemp_lifeSheetCreated);
    }

    /**
     * Test Conception Number: 19
     * Successfully save as to be validated from add menu a file linked to a validated equipment
     * File name: "File"
     * File location: /
     * Expected result: The file is correctly added in the database and the version of the equipment is changed
     * @return void
    */
    public function test_add_file_to_be_validated_success_validated_eq()
    {
        $equipment = $this->make_an_equipment('addTBVEqVTest', 'validated');
        $actualVersion = EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addTBVEqVTest')->last()->eqTemp_version;
        // Add a file to the equipment
        $response = $this->post('file/verif', [
            'file_validate' => 'to_be_validated',
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $countFile = File::all()->count();
        $this->post('/equipment/add/file/', [
            'file_validate' => 'to_be_validated',
            'eq_id' => $equipment->id,
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countFile+1, File::all()->count());
        $this->assertDatabaseHas('files', [
            'file_name' => 'File',
            'file_validate' => 'to_be_validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id
        ]);
        $this->assertEquals($actualVersion+1, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addTBVEqVTest')->last()->eqTemp_version);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addTBVEqVTest')->last()->technicalVerifier_id);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addTBVEqVTest')->last()->qualityVerifier_id);
        $this->assertEquals(0, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addTBVEqVTest')->last()->eqTemp_lifeSheetCreated);
    }

    /**
     * Test Conception Number: 20
     * Successfully save as to be validated from add menu a file linked to a validated mme
     * File name: "File"
     * File location: /
     * Expected result: The file is correctly added in the database and the version of the mme is changed
     * @return void
    */
    public function test_add_file_to_be_validated_success_validated_mme()
    {
        $Mme = $this->make_a_mme('addTBVMmeVTest', 'validated');
        $actualVersion = MmeTemp::all()->where('mmeTemp_remarks', '==', 'addTBVMmeVTest')->last()->mmeTemp_version;
        // Add a file to the equipment
        $response = $this->post('file/verif', [
            'file_validate' => 'to_be_validated',
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $countFile = File::all()->count();
        $this->post('/mme/add/file/', [
            'file_validate' => 'to_be_validated',
            'mme_id' => $Mme->id,
            'file_name' => 'File'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countFile+1, File::all()->count());
        $this->assertDatabaseHas('files', [
            'file_name' => 'File',
            'file_validate' => 'to_be_validated',
            'mmeTemp_id' => $Mme->id
        ]);
        $this->assertEquals($actualVersion+1, MmeTemp::all()->where('mmeTemp_remarks', '==', 'addTBVMmeVTest')->last()->mmeTemp_version);
        $this->assertNull(MmeTemp::all()->where('mmeTemp_remarks', '==', 'addTBVMmeVTest')->last()->technicalVerifier_id);
        $this->assertNull(MmeTemp::all()->where('mmeTemp_remarks', '==', 'addTBVMmeVTest')->last()->qualityVerifier_id);
        $this->assertEquals(0, MmeTemp::all()->where('mmeTemp_remarks', '==', 'addTBVMmeVTest')->last()->mmeTemp_lifeSheetCreated);
    }

    /**
     * Test Conception Number: 21
     * Successfully save as validated from add menu a file linked to a validated equipment
     * File name: "File"
     * File location: "FilePath"
     * Expected result: The file is correctly added in the database and the version of the equipment is changed
     * @return void
    */
    public function test_add_file_validated_success_validated_eq()
    {
        $equipment = $this->make_an_equipment('addValEqVTest', 'validated');
        $actualVersion = EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addValEqVTest')->last()->eqTemp_version;
        // Add a file to the equipment
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'file_location' => "FilePath"
        ]);
        $response->assertStatus(200);
        $countFile = File::all()->count();
        $this->post('/equipment/add/file/', [
            'file_validate' => 'validated',
            'eq_id' => $equipment->id,
            'file_name' => 'File',
            'file_location' => "FilePath"
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countFile+1, File::all()->count());
        $this->assertDatabaseHas('files', [
            'file_name' => 'File',
            'file_location' => "FilePath",
            'file_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id
        ]);
        $this->assertEquals($actualVersion+1, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addValEqVTest')->last()->eqTemp_version);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addValEqVTest')->last()->technicalVerifier_id);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addValEqVTest')->last()->qualityVerifier_id);
        $this->assertEquals(0, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addValEqVTest')->last()->eqTemp_lifeSheetCreated);
    }

    /**
     * Test Conception Number: 22
     * Successfully save as validated from add menu a file linked to a validated mme
     * File name: "File"
     * File location: "FilePath"
     * Expected result: The file is correctly added in the database and the version of the mme is changed
     * @return void
    */
    public function test_add_file_validated_success_validated_mme()
    {
        $Mme = $this->make_a_mme('addValMmeVTest', 'validated');
        $actualVersion = MmeTemp::all()->where('mmeTemp_remarks', '==', 'addValMmeVTest')->last()->mmeTemp_version;
        // Add a file to the equipment
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'file_location' => "FilePath"
        ]);
        $response->assertStatus(200);
        $countFile = File::all()->count();
        $this->post('/mme/add/file/', [
            'file_validate' => 'validated',
            'mme_id' => $Mme->id,
            'file_name' => 'File',
            'file_location' => "FilePath"
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countFile+1, File::all()->count());
        $this->assertDatabaseHas('files', [
            'file_name' => 'File',
            'file_location' => "FilePath",
            'file_validate' => 'validated',
            'mmeTemp_id' => $Mme->id
        ]);
        $this->assertEquals($actualVersion+1, MmeTemp::all()->where('mmeTemp_remarks', '==', 'addValMmeVTest')->last()->mmeTemp_version);
        $this->assertNull(MmeTemp::all()->where('mmeTemp_remarks', '==', 'addValMmeVTest')->last()->technicalVerifier_id);
        $this->assertNull(MmeTemp::all()->where('mmeTemp_remarks', '==', 'addValMmeVTest')->last()->qualityVerifier_id);
        $this->assertEquals(0, MmeTemp::all()->where('mmeTemp_remarks', '==', 'addValMmeVTest')->last()->mmeTemp_lifeSheetCreated);
    }

    public function make_an_eq_with_file($name, $validate)
    {
        if ($validate != "validated") {
            $equipment = $this->make_an_equipment($name, $validate);
            // Add a file to the equipment
            $response = $this->post('file/verif', [
                'file_validate' => 'validated',
                'file_name' => 'File',
                'file_location' => 'FilePath'
            ]);
            $response->assertStatus(200);
            $countFile = File::all()->count();
            $this->post('/equipment/add/file/', [
                'file_validate' => 'validated',
                'eq_id' => $equipment->id,
                'file_name' => 'File',
                'file_location' => 'FilePath'
            ]);
            $response->assertStatus(200);
            $this->assertEquals($countFile+1, File::all()->count());
            $this->assertDatabaseHas('files', [
                'file_name' => 'File',
                'file_location' => 'FilePath',
                'file_validate' => 'validated',
                'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id
            ]);
            return $equipment;
        }
        $equipment = $this->make_an_equipment($name, 'validated');
        // Add a file to the equipment
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'file_location' => 'FilePath'
        ]);
        $response->assertStatus(200);
        $countFile = File::all()->count();
        $this->post('/equipment/add/file/', [
            'file_validate' => 'validated',
            'eq_id' => $equipment->id,
            'file_name' => 'File',
            'file_location' => 'FilePath'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countFile+1, File::all()->count());
        $this->assertDatabaseHas('files', [
            'file_name' => 'File',
            'file_location' => 'FilePath',
            'file_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id
        ]);
        $this->make_an_eq_verif($equipment->id);
        return $equipment;
    }

    public function make_a_mme_with_file($name, $validate)
    {
        if ($validate != "validated") {
            $Mme = $this->make_a_mme($name, $validate);
            // Add a file to the equipment
            $response = $this->post('file/verif', [
                'file_validate' => 'validated',
                'file_name' => 'File',
                'file_location' => "FilePath"
            ]);
            $response->assertStatus(200);
            $countFile = File::all()->count();
            $this->post('/mme/add/file/', [
                'file_validate' => 'validated',
                'mme_id' => $Mme->id,
                'file_name' => 'File',
                'file_location' => "FilePath"
            ]);
            $response->assertStatus(200);
            $this->assertEquals($countFile+1, File::all()->count());
            $this->assertDatabaseHas('files', [
                'file_name' => 'File',
                'file_location' => "FilePath",
                'file_validate' => 'validated',
                'mmeTemp_id' => $Mme->id
            ]);
            return $Mme;
        }
        $Mme = $this->make_a_mme($name, 'validated');
        // Add a file to the equipment
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'File',
            'file_location' => "FilePath"
        ]);
        $response->assertStatus(200);
        $countFile = File::all()->count();
        $this->post('/mme/add/file/', [
            'file_validate' => 'validated',
            'mme_id' => $Mme->id,
            'file_name' => 'File',
            'file_location' => "FilePath"
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countFile+1, File::all()->count());
        $this->assertDatabaseHas('files', [
            'file_name' => 'File',
            'file_location' => "FilePath",
            'file_validate' => 'validated',
            'mmeTemp_id' => $Mme->id
        ]);
        $this->make_a_mme_verif($Mme->id);
        return $Mme;
    }


    /**
     * Test Conception Number: 23
     * Successfully update as drafted a file linked to an equipment
     * File name: "NewFile"
     * File location: "NewFilePath"
     * Expected result: The file is correctly updated in the database
     * @return void
     */
    public function test_update_file_drafted_success_update_eq()
    {
        $equipment = $this->make_an_eq_with_file('upDraftEqTest', 'drafted');
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath"
        ]);
        $response->assertStatus(200);
        $file = File::all()->where('equipmentTemp_id', '==', EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id)->last();
        $this->post('/equipment/update/file/'.$file->id, [
            'file_validate' => 'drafted',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'eq_id' => $equipment->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('files', [
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'file_validate' => 'drafted',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id
        ]);
    }

    /**
     * Test Conception Number: 24
     * Successfully update as drafted a file linked to a mme
     * File name: "NewFile"
     * File location: "NewFilePath"
     * Expected result: The file is correctly updated in the database
     * @return void
    */
    public function test_update_file_drafted_success_update_mme()
    {
        $Mme = $this->make_a_mme_with_file('upDraftMmeTest', 'drafted');
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath"
        ]);
        $response->assertStatus(200);
        $file = File::all()->where('mmeTemp_id', '==', $Mme->id)->last();
        $this->post('/mme/update/file/'.$file->id, [
            'file_validate' => 'drafted',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'mme_id' => $Mme->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('files', [
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'file_validate' => 'drafted',
            'mmeTemp_id' => $Mme->id
        ]);
    }

    /**
     * Test Conception Number: 25
     * Successfully update as to be validated a file linked to an equipment
     * File name: "NewFile"
     * File location: "NewFilePath"
     * Expected result: The file is correctly updated in the database
     * @return void
    */
    public function test_update_file_to_be_validated_success_update_eq()
    {
        $equipment = $this->make_an_eq_with_file('upTBVEqTest', 'drafted');
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath"
        ]);
        $response->assertStatus(200);
        $file = File::all()->where('equipmentTemp_id', '==', EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id)->last();
        $this->post('/equipment/update/file/'.$file->id, [
            'file_validate' => 'to_be_validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'eq_id' => $equipment->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('files', [
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'file_validate' => 'to_be_validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id
        ]);
    }

    /**
     * Test Conception Number: 26
     * Successfully update as to be validated a file linked to a mme
     * File name: "NewFile"
     * File location: "NewFilePath"
     * Expected result: The file is correctly updated in the database
     * @return void
    */
    public function test_update_file_to_be_validated_success_update_mme()
    {
        $Mme = $this->make_a_mme_with_file('upTBVMmeTest', 'drafted');
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath"
        ]);
        $response->assertStatus(200);
        $file = File::all()->where('mmeTemp_id', '==', $Mme->id)->last();
        $this->post('/mme/update/file/'.$file->id, [
            'file_validate' => 'to_be_validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'mme_id' => $Mme->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('files', [
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'file_validate' => 'to_be_validated',
            'mmeTemp_id' => $Mme->id
        ]);
    }

    /**
     * Test Conception Number: 27
     * Successfully update as validated a file linked to an equipment
     * File name: "NewFile"
     * File location: "NewFilePath"
     * Expected result: The file is correctly updated in the database
     * @return void
    */
    public function test_update_file_validated_success_update_eq()
    {
        $equipment = $this->make_an_eq_with_file('upValEqTest', 'drafted');
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath"
        ]);
        $response->assertStatus(200);
        $file = File::all()->where('equipmentTemp_id', '==', EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id)->last();
        $this->post('/equipment/update/file/'.$file->id, [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'eq_id' => $equipment->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('files', [
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'file_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id
        ]);
    }

    /**
     * Test Conception Number: 28
     * Successfully update as validated a file linked to a mme
     * File name: "NewFile"
     * File location: "NewFilePath"
     * Expected result: The file is correctly updated in the database
     * @return void
    */
    public function test_update_file_validated_success_update_mme()
    {
        $Mme = $this->make_a_mme_with_file('upValMmeTest', 'drafted');
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath"
        ]);
        $response->assertStatus(200);
        $file = File::all()->where('mmeTemp_id', '==', $Mme->id)->last();
        $this->post('/mme/update/file/'.$file->id, [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'mme_id' => $Mme->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('files', [
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'file_validate' => 'validated',
            'mmeTemp_id' => $Mme->id
        ]);
    }

    /**
     * Test Conception Number: 29
     * Successfully update as drafted a file linked to a validated equipment
     * File name: "NewFile"
     * File location: "NewFilePath"
     * Expected result: The file is correctly added in the database and the version of the equipment is changed
     * @return void
    */
    public function test_update_file_drafted_success_update_validated_eq()
    {
        $equipment = $this->make_an_eq_with_file('upDrEqValTest', 'validated');
        $this->assertEquals(0, 0);
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath"
        ]);
        $response->assertStatus(200);
        $file = File::all()->where('equipmentTemp_id', '==', EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id)->last();
        $this->post('/equipment/update/file/'.$file->id, [
            'file_validate' => 'drafted',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'eq_id' => $equipment->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('files', [
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'file_validate' => 'drafted',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => $equipment->id,
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_version' => $equipment->eq_nbrVersion + 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null
        ]);
    }

    /**
     * Test Conception Number: 30
     * Successfully update as drafted a file linked to a validated mme
     * File name: "NewFile"
     * File location: "NewFilePath"
     * Expected result: The file is correctly added in the database and the version of the mme is changed
     * @return void
    */
    public function test_update_file_drafted_success_update_validated_mme()
    {
        $Mme = $this->make_a_mme_with_file('upDrMmeValTest', 'validated');
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath"
        ]);
        $response->assertStatus(200);
        $file = File::all()->where('mmeTemp_id', '==', $Mme->id)->last();
        $this->post('/mme/update/file/'.$file->id, [
            'file_validate' => 'drafted',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'mme_id' => $Mme->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('files', [
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'file_validate' => 'drafted',
            'mmeTemp_id' => $Mme->id
        ]);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => $Mme->id,
            'mmeTemp_version' => $Mme->mme_nbrVersion + 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null
        ]);
    }

    /**
     * Test Conception Number: 31
     * Successfully update as to be validated a file linked to a validated equipment
     * File name: "NewFile"
     * File location: "NewFilePath"
     * Expected result: The file is correctly added in the database and the version of the equipment is changed
     * @return void
    */
    public function test_update_file_to_be_validated_success_update_validated_eq()
    {
        $equipment = $this->make_an_eq_with_file('upTBVEqValTest', 'validated');
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath"
        ]);
        $response->assertStatus(200);
        $file = File::all()->where('equipmentTemp_id', '==', EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id)->last();
        $this->post('/equipment/update/file/'.$file->id, [
            'file_validate' => 'to_be_validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'eq_id' => $equipment->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('files', [
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'file_validate' => 'to_be_validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => $equipment->id,
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_version' => $equipment->eq_nbrVersion + 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null
        ]);
    }

    /**
     * Test Conception Number: 32
     * Successfully update as to be validated a file linked to a validated mme
     * File name: "NewFile"
     * File location: "NewFilePath"
     * Expected result: The file is correctly added in the database and the version of the mme is changed
     * @return void
    */
    public function test_update_file_to_be_validated_success_update_validated_mme()
    {
        $Mme = $this->make_a_mme_with_file('upTBVMmeValTest', 'validated');
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath"
        ]);
        $response->assertStatus(200);
        $file = File::all()->where('mmeTemp_id', '==', $Mme->id)->last();
        $this->post('/mme/update/file/'.$file->id, [
            'file_validate' => 'to_be_validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'mme_id' => $Mme->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('files', [
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'file_validate' => 'to_be_validated',
            'mmeTemp_id' => $Mme->id
        ]);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => $Mme->id,
            'mmeTemp_version' => $Mme->mme_nbrVersion + 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null
        ]);
    }

    /**
     * Test Conception Number: 33
     * Successfully update as validated a file linked to a validated equipment
     * File name: "NewFile"
     * File location: "NewFilePath"
     * Expected result: The file is correctly added in the database and the version of the equipment is changed
     * @return void
    */
    public function test_update_file_validated_success_update_validated_eq()
    {
        $equipment = $this->make_an_eq_with_file('upValEqValTest', 'validated');
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath"
        ]);
        $response->assertStatus(200);
        $file = File::all()->where('equipmentTemp_id', '==', EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id)->last();
        $this->post('/equipment/update/file/'.$file->id, [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'eq_id' => $equipment->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('files', [
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'file_validate' => 'validated',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', $equipment->id)->last()->id
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => $equipment->id,
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_version' => $equipment->eq_nbrVersion + 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null
        ]);
    }

    /**
     * Test Conception Number: 34
     * Successfully update as validated a file linked to a validated mme
     * File name: "NewFile"
     * File location: "NewFilePath"
     * Expected result: The file is correctly added in the database and the version of the mme is changed
     * @return void
    */
    public function test_update_file_validated_success_update_validated_mme()
    {
        $Mme = $this->make_a_mme_with_file('upValMmeValTest', 'validated');
        $response = $this->post('file/verif', [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath"
        ]);
        $response->assertStatus(200);
        $file = File::all()->where('mmeTemp_id', '==', $Mme->id)->last();
        $this->post('/mme/update/file/'.$file->id, [
            'file_validate' => 'validated',
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'mme_id' => $Mme->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('files', [
            'file_name' => 'NewFile',
            'file_location' => "NewFilePath",
            'file_validate' => 'validated',
            'mmeTemp_id' => $Mme->id
        ]);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => $Mme->id,
            'mmeTemp_version' => $Mme->mme_nbrVersion + 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null
        ]);
    }

    /**
     * Test Conception Number: 35
     * Successfully send the file list for an equipment
     * @returns void
    */
    public function test_send_file_list_for_an_equipment()
    {
        $equipment = $this->make_an_eq_with_file('FileListEqTest', 'validated');
        $response = $this->get('/file/send/'.$equipment->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'file_name' => 'File',
                'file_location' => 'FilePath',
                'file_validate' => 'validated'
            ]
        ]);
    }

    /**
     * Test Conception Number: 36
     * Successfully send the file list for a mme
     * @returns void
     */
    public function test_send_file_list_for_a_mme()
    {
        $Mme = $this->make_a_mme_with_file('FileListMmeTest', 'validated');
        $response = $this->get('/file/send/mme/'.$Mme->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'file_name' => 'File',
                'file_location' => 'FilePath',
                'file_validate' => 'validated'
            ]
        ]);
    }

    /*
     * Test Conception Number 37
     * Successfully delete a file linked to an equipment
     * Expected result: The file is correctly removed in the database
     * @returns void
    */
    public function test_delete_file_from_an_eq()
    {
        $equipment = $this->make_an_eq_with_file('delDrEqTest','drafted');
        $lastEquipmentTemps = EquipmentTemp::all()->where('equipment_id', '==', $equipment->id)->last()->id;
        $fileID = File::all()->where('equipmentTemp_id', '==', $lastEquipmentTemps)->first()->id;
        $this->post('/equipment/delete/file/'.$fileID, [
            'eq_id' => $equipment->id
        ]);
        $this->assertDatabaseMissing('files', [
            'id' => $fileID,
            'equipmentTemp_id' => $lastEquipmentTemps
        ]);
    }

    /**
     * Test Conception Number 38
     * Successfully delete a file linked to a mme
     * Expected result: The file is correctly removed in the database
     * @returns void
    */
    public function test_delete_file_from_a_mme()
    {
        $Mme = $this->make_a_mme_with_file('delDrMmeTest','drafted');
        $lastMmeTemps = MmeTemp::all()->where('mme_id', '==', $Mme->id)->last()->id;
        $fileID = File::all()->where('mmeTemp_id', '==', $lastMmeTemps)->first()->id;
        $this->post('/mme/delete/file/'.$fileID, [
            'mme_id' => $Mme->id
        ]);
        $this->assertDatabaseMissing('files', [
            'id' => $fileID,
            'mmeTemp_id' => $lastMmeTemps
        ]);
    }

    /**
     * Test Conception Number 39
     * Successfully delete a file linked to an validated equipment
     * Expected result: The file is correctly added in the database and the version of the equipment is changed
     * @returns void
    */
    public function test_delete_file_from_an_valid_eq()
    {
        $equipment = $this->make_an_eq_with_file('delValEqTest','validated');
        $lastEquipmentTemps = EquipmentTemp::all()->where('equipment_id', '==', $equipment->id)->last()->id;
        $fileID = File::all()->where('equipmentTemp_id', '==', $lastEquipmentTemps)->first()->id;
        $this->post('/equipment/delete/file/'.$fileID, [
            'eq_id' => $equipment->id
        ]);
        $this->assertDatabaseMissing('files', [
            'id' => $fileID,
            'equipmentTemp_id' => $lastEquipmentTemps
        ]);
        $this->assertDatabaseHas('equipment_temps',[
            'id' => $lastEquipmentTemps,
            'equipment_id' => $equipment->id,
            'eqTemp_version' => 3
        ]);
    }

    /**
     * Test Conception Number 40
     * Successfully delete a file linked to a validated mme
     * Expected result: The file is correctly added in the database and the version of the mme is changed
     * @returns void
    */
    public function test_delete_file_from_a_valid_mme()
    {
        $Mme = $this->make_a_mme_with_file('delValMmeTest','validated');
        $lastMmeTemps = MmeTemp::all()->where('mme_id', '==', $Mme->id)->last()->id;
        $fileID = File::all()->where('mmeTemp_id', '==', $lastMmeTemps)->first()->id;
        $this->post('/mme/delete/file/'.$fileID, [
            'mme_id' => $Mme->id
        ]);
        $this->assertDatabaseMissing('files', [
            'id' => $fileID,
            'mmeTemp_id' => $lastMmeTemps
        ]);
        $this->assertDatabaseHas('mme_temps',[
            'id' => $lastMmeTemps,
            'mme_id' => $Mme->id,
            'mmeTemp_version' => 3
        ]);
    }
}
