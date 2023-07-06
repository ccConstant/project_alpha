<?php

/**
 * Filename: EquipmentTest.php
 * Creation date: 21 Apr 2023
 * Update date: 21 Apr 2023
 * This file contains all the tests about the equipment table.
 * Coverage : 100%
 */

namespace Tests\Feature;

use App\Models\SW01\EnumEquipmentMassUnit;
use App\Models\SW01\EnumEquipmentType;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\PreventiveMaintenanceOperation;
use App\Models\SW01\State;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EquipmentTest extends TestCase
{
    use RefreshDatabase;

    public function create_user($name) {
        if (User::all()->count() == 0) {
            $countUser = User::all()->count();
            $response = $this->post('register', [
                'user_firstName' => $name,
                'user_lastName' => $name,
                'user_pseudo' => $name,
                'user_password' => 'VerifierVerifier',
                'user_confirmation_password' => 'VerifierVerifier',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countUser + 1, User::all());

            User::all()->last()->update([
                'user_menuUserAcessRight' => 1,
                'user_resetUserPasswordRight' => 1,
                'user_updateDataInDraftRight' => 1,
                'user_validateDescriptiveLifeSheetDataRight' => 1,
                'user_validateOtherDataRight' => 1,
                'user_updateDataValidatedButNotSignedRight' => 1,
                'user_updateDescriptiveLifeSheetDataSignedRight' => 1,
                'user_makeQualityValidationRight' => 1,
                'user_makeTechnicalValidationRight' => 1,
                'user_deleteDataNotValidatedLinkedToEqOrMmeRight' => 1,
                'user_deleteDataValidatedLinkedToEqOrMmeRight' => 1,
                'user_deleteDataSignedLinkedToEqOrMmeRight' => 1,
                'user_deleteEqOrMmeRight' => 1,
                'user_makeReformRight' => 1,
                'user_declareNewStateRight' => 1,
                'user_updateEnumRight' => 1,
                'user_deleteEnumRight' => 1,
                'user_addEnumRight' => 1,
                'user_updateInformationRight' => 1,
                'user_makeEqOpValidationRight' => 1,
                'user_personTrainedToGeneralPrinciplesOfEqManagementRight' => 1,
                'user_makeEqRespValidationRight' => 1,
                'user_personTrainedToGeneralPrinciplesOfMMEManagementRight' => 1,
                'user_makeMmeOpValidationRight' => 1,
                'user_makeMmeRespValidationRight' => 1,
            ]);
        }
        return User::all()->last()->id;
    }

    /**
     * Test Conception Number: 1
     * Add new equipment as drafted with no values
     * Internal Ref: /
     * Name: /
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter an internal reference"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_drafted_no_values()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_internalReference' => 'You must enter an internal reference',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 2
     * Add new equipment as drafted with a too short internal ref
     * Internal Ref: "in"
     * Name: /
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_drafted_short_intern_ref()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_internalReference' => 'You must enter at least 3 characters',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 3
     * Add a new equipment as drafted with a too long internal ref
     * Internal Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Name: /
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 16 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_drafted_long_intern_ref()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_internalReference' => 'You must enter a maximum of 16 characters',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 4
     * Add a new equipment as drafted with a too long name
     * Internal Ref: "three"
     * Name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor."
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_drafted_long_name()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_name' => 'You must enter a maximum of 100 characters',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 5
     * Add a new equipment as drafted with a too short external ref
     * Internal Ref: "three"
     * Name: /
     * External Ref: "in"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     * @returns void
     */
    public function test_add_equipment_drafted_short_alpha_ref()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_externalReference' => 'You must enter at least 3 characters'
        ]);
    }

    /**
     * Test Conception Number: 6
     * Add a new equipment as drafted with a too long external ref
     * Internal Ref: "three"
     * Name: /
     * External Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor."
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     * @returns void
     */
    public function test_add_equipment_drafted_long_alpha_ref()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_externalReference' => 'You must enter a maximum of 100 characters'
        ]);
    }

    /**
     * Test Conception Number: 7
     * Add a new equipment as drafted with a too long serial number
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_equipment_drafted_long_serial_number()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_serialNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_serialNumber' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 8
     * Add a new equipment as drafted with a too long constructor
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 30 characters"
     * @returns void
     */
    public function test_add_equipment_drafted_long_constructor()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_constructor' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_constructor' => 'You must enter a maximum of 30 characters'
        ]);
    }

    /**
     * Test Conception Number: ç
     * Add a new equipment as drafted with a too long mass
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 8 characters"
     * @returns void
     */
    public function test_add_equipment_drafted_long_mass()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_mass' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_mass' => 'You must enter a maximum of 8 characters'
        ]);
    }

    /**
     * Test Conception Number: 10
     * Add a new equipment as drafted with a too long set
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit:
     * Mobil ? : /
     * Remarks: /
     * Set: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 20 characters"
     * @returns void
     */
    public function test_add_equipment_drafted_long_set()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_set' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_set' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 11
     * Successfully saved a new equipment as drafted
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: The equipment is saved as drafted and successfully added in the database
     * @returns void
     */
    public function test_add_equipment_drafted_success()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(200);
        $this->post('/equipment/add', [
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment', [
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three'
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
    }

    /**
     * Test Conception Number: 12
     * Add a new equipment as to be validated with no values
     * Internal Ref: /
     * Name: /
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter an internal reference"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_no_values()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_internalReference' => 'You must enter an internal reference',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 13
     * Add a new equipment as to be validated with a too short internal reference
     * Internal Ref: "in"
     * Name: /
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_short_internal_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_internalReference' => 'You must enter at least 3 characters',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 14
     * Add a new equipment as to be validated with a too long internal reference
     * Internal Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Name: /
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 16 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_long_internal_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_internalReference' => 'You must enter a maximum of 16 characters',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 15
     * Add a new equipment as to be validated with a too long name
     * Internal Ref: "three"
     * Name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. "
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_long_name()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_name' => 'You must enter a maximum of 100 characters',
            'eq_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 16
     * Add a new equipment as to be validated with a too short external reference
     * Internal Ref: "three"
     * Name: /
     * External Ref: "in"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_short_external_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_externalReference' => 'You must enter at least 3 characters'
        ]);
    }

    /**
     * Test Conception Number: 17
     * Add a new equipment as to be validated with a too long external reference
     * Internal Ref: "three"
     * Name: /
     * External Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. "
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_long_external_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_externalReference' => 'You must enter a maximum of 100 characters'
        ]);
    }

    /**
     * Test Conception Number: 18
     * Add a new equipment as to be validated with a too long serial number
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_long_serial_number()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_serialNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_serialNumber' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 19
     * Add a new equipment as to be validated with a too long constructor
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_long_constructor()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_constructor' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_constructor' => 'You must enter a maximum of 30 characters'
        ]);
    }

    /**
     * Test Conception Number: 20
     * Add a new equipment as to be validated with a too long mass
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_long_mass()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_mass' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_mass' => 'You must enter a maximum of 8 characters'
        ]);
    }

    /**
     * Test Conception Number: 21
     * Add new equipment as to be validated with a too long set
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 20 characters"
     * @returns void
     */
    public function test_add_equipment_to_be_validated_long_set()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_set' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_set' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 22
     * Successfully add new equipment as to be validated and check if it is in the database
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: The equipment is in the database
     * @returns void
     */
    public function test_add_equipment_to_be_validated_success()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment', [
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three'
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
    }

    /**
     * Test Conception Number: 23
     * Add a new equipment as validated with no values
     * Internal Ref: /
     * Name: /
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter an internal reference"
     *                                      "You must enter an external reference"
     *                                      "You must enter a name"
     *                                      "You must enter a serial number"
     *                                      "You must enter a constructor"
     *                                      "You must enter a mass"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_equipment_validated_no_values()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_internalReference' => 'You must enter an internal reference',
            'eq_externalReference' => 'You must enter an external reference',
            'eq_name' => 'You must enter a name',
            'eq_serialNumber' => 'You must enter a serial number',
            'eq_constructor' => 'You must enter a constructor',
            'eq_mass' => 'You must enter a mass',
            'eq_remarks' => 'You must enter a remark',
            'eq_set' => 'You must enter a set',
            'eq_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 24
     * Add a new equipment as validated with a too short internal reference
     * Internal Ref: "in"
     * Name: /
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 caracters"
     *                                      "You must enter an external reference"
     *                                      "You must enter a name"
     *                                      "You must enter a serial number"
     *                                      "You must enter a constructor"
     *                                      "You must enter a mass"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_equipment_validated_short_internal_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_internalReference' => 'You must enter at least 3 characters',
            'eq_externalReference' => 'You must enter an external reference',
            'eq_name' => 'You must enter a name',
            'eq_serialNumber' => 'You must enter a serial number',
            'eq_constructor' => 'You must enter a constructor',
            'eq_mass' => 'You must enter a mass',
            'eq_remarks' => 'You must enter a remark',
            'eq_set' => 'You must enter a set',
            'eq_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 25
     * Add a new equipment as validated with a too long internal reference
     * Internal Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Name: /
     * External Ref: /
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 16 characters"
     *                                      "You must enter an external reference"
     *                                      "You must enter a name"
     *                                      "You must enter a serial number"
     *                                      "You must enter a constructor"
     *                                      "You must enter a mass"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_equipment_validated_long_internal_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_internalReference' => 'You must enter a maximum of 16 characters',
            'eq_externalReference' => 'You must enter an external reference',
            'eq_name' => 'You must enter a name',
            'eq_serialNumber' => 'You must enter a serial number',
            'eq_constructor' => 'You must enter a constructor',
            'eq_mass' => 'You must enter a mass',
            'eq_remarks' => 'You must enter a remark',
            'eq_set' => 'You must enter a set',
            'eq_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 26
     * Add a new equipment as validated with a too short external reference
     * Internal Ref: "three"
     * Name: /
     * External Ref: "in"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 caracters"
     *                                      "You must enter a name"
     *                                      "You must enter a serial number"
     *                                      "You must enter a constructor"
     *                                      "You must enter a mass"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_equipment_validated_short_external_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_externalReference' => 'You must enter at least 3 characters',
            'eq_name' => 'You must enter a name',
            'eq_serialNumber' => 'You must enter a serial number',
            'eq_constructor' => 'You must enter a constructor',
            'eq_mass' => 'You must enter a mass',
            'eq_remarks' => 'You must enter a remark',
            'eq_set' => 'You must enter a set',
            'eq_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 27
     * Add a new equipment as validated with a too long external reference
     * Internal Ref: "three"
     * Name: /
     * External Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     *                                      "You must enter a name"
     *                                      "You must enter a serial number"
     *                                      "You must enter a constructor"
     *                                      "You must enter a mass"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_equipment_validated_long_external_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_externalReference' => 'You must enter a maximum of 100 characters',
            'eq_name' => 'You must enter a name',
            'eq_serialNumber' => 'You must enter a serial number',
            'eq_constructor' => 'You must enter a constructor',
            'eq_mass' => 'You must enter a mass',
            'eq_remarks' => 'You must enter a remark',
            'eq_set' => 'You must enter a set',
            'eq_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 28
     * Add a new equipment as validated with a too short name
     * Internal Ref: "three"
     * Name: "in"
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 caracters"
     *                                      "You must enter a serial number"
     *                                      "You must enter a constructor"
     *                                      "You must enter a mass"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_equipment_validated_short_name()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_name' => 'You must enter at least 3 characters',
            'eq_serialNumber' => 'You must enter a serial number',
            'eq_constructor' => 'You must enter a constructor',
            'eq_mass' => 'You must enter a mass',
            'eq_remarks' => 'You must enter a remark',
            'eq_set' => 'You must enter a set',
            'eq_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 29
     * Add a new equipment as validated with a too long name
     * Internal Ref: "three"
     * Name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * External Ref: "three"
     * Type: /
     * Serial Number: /
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     *                                      "You must enter a serial number"
     *                                      "You must enter a constructor"
     *                                      "You must enter a mass"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_equipment_validated_long_name()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_name' => 'You must enter a maximum of 100 characters',
            'eq_serialNumber' => 'You must enter a serial number',
            'eq_constructor' => 'You must enter a constructor',
            'eq_mass' => 'You must enter a mass',
            'eq_remarks' => 'You must enter a remark',
            'eq_set' => 'You must enter a set',
            'eq_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 30
     * Add a new equipment as validated with a too short serial number
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Type: /
     * Serial Number: "in"
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 caracters"
     *                                      "You must enter a constructor"
     *                                      "You must enter a mass"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_equipment_validated_short_serial_number()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_serialNumber' => 'You must enter at least 3 characters',
            'eq_constructor' => 'You must enter a constructor',
            'eq_mass' => 'You must enter a mass',
            'eq_remarks' => 'You must enter a remark',
            'eq_set' => 'You must enter a set',
            'eq_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 31
     * Add a new equipment as validated with a too long serial number
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Type: /
     * Serial Number: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Constructor: /
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     *                                      "You must enter a constructor"
     *                                      "You must enter a mass"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_equipment_validated_long_serial_number()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_serialNumber' => 'You must enter a maximum of 50 characters',
            'eq_constructor' => 'You must enter a constructor',
            'eq_mass' => 'You must enter a mass',
            'eq_remarks' => 'You must enter a remark',
            'eq_set' => 'You must enter a set',
            'eq_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 32
     * Add a new equipment as validated with a too short constructor
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Type: /
     * Serial Number: "three"
     * Constructor: "in"
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 caracters"
     *                                      "You must enter a mass"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_equipment_validated_short_constructor()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_constructor' => 'You must enter at least 3 characters',
            'eq_mass' => 'You must enter a mass',
            'eq_remarks' => 'You must enter a remark',
            'eq_set' => 'You must enter a set',
            'eq_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 33
     * Add a new equipment as validated with a too long constructor
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Type: /
     * Serial Number: "three"
     * Constructor: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Mass: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 30 characters"
     *                                      "You must enter a mass"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_equipment_validated_long_constructor()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_constructor' => 'You must enter a maximum of 30 characters',
            'eq_mass' => 'You must enter a mass',
            'eq_remarks' => 'You must enter a remark',
            'eq_set' => 'You must enter a set',
            'eq_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 34
     * Add a new equipment as validated with a too long mass
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Type: /
     * Serial Number: "three"
     * Constructor: "three
     * Mass: 123456789123456789
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 30 characters"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_equipment_validated_long_mass()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => '123456789123456789',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_mass' => 'You must enter a maximum of 8 characters',
            'eq_remarks' => 'You must enter a remark',
            'eq_set' => 'You must enter a set',
            'eq_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 35
     * Add a new equipment as validated with a too short remark
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Type: /
     * Serial Number: "three"
     * Constructor: "three"
     * Mass: 1234
     * Unit: /
     * Mobil ? : /
     * Remarks: "in"
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 caracters"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_equipment_validated_short_remark()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_remarks' => 'You must enter at least 3 characters',
            'eq_set' => 'You must enter a set',
            'eq_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 36
     * Add a new equipment as validated with a too long remark
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Type: /
     * Serial Number: "three"
     * Constructor: "three"
     * Mass: 1234
     * Unit: /
     * Mobil ? : /
     * Remarks: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 400 characters"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_equipment_validated_long_remark()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_remarks' => 'You must enter a maximum of 400 characters',
            'eq_set' => 'You must enter a set',
            'eq_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 37
     * Add a new equipment as validated with a too long set
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Type: /
     * Serial Number: "three"
     * Constructor: "three"
     * Mass: 1234
     * Unit: /
     * Mobil ? : /
     * Remarks: "three"
     * Set: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_equipment_validated_long_set()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_set' => 'You must enter a maximum of 50 characters',
            'eq_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 38
     * Add a new equipment as validated with a too long location
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Type: /
     * Serial Number: "three"
     * Constructor: "three"
     * Mass: 1234
     * Unit: /
     * Mobil ? : /
     * Remarks: "three"
     * Set: "three"
     * Location: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_equipment_validated_long_location()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_location' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'eq_location' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 39
     * Add a new equipment as validated with correct values but no type
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Type: /
     * Serial Number: "three"
     * Constructor: "three"
     * Mass: 1234
     * Unit: /
     * Mobil ? : /
     * Remarks: "three"
     * Set: "three"
     * Location: "three"
     * Expected Result: Receiving an error:
     *                                      "You must choose a type"
     * @returns void
     */
    public function test_add_equipment_validated_no_type()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_location' => 'three',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'eq_type' => 'You must choose a type',
        ]);
    }

    /**
     * Test Conception Number: 40
     * Add a new equipment as validated with correct values but no mass unit
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Type: "three"
     * Serial Number: "three"
     * Constructor: "three"
     * Mass: 1234
     * Unit: /
     * Mobil ? : /
     * Remarks: "three"
     * Set: "three"
     * Location: "three"
     * Expected Result: Receiving an error:
     *                                      "You must choose a unit of mass"
     * @returns void
     */
    public function test_add_equipment_validated_no_unit()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_location' => 'three',
            'eq_type' => 'three',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'eq_massUnit' => 'You must choose a unit of mass',
        ]);
    }

    /**
     * Test Conception Number: 41
     * Add a new equipment as validated with correct values
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Type: "three"
     * Serial Number: "three"
     * Constructor: "three"
     * Mass: 1234
     * Unit: "three"
     * Mobil ? : /
     * Remarks: "three"
     * Set: "three"
     * Location: "three"
     * Expected Result: The equipment is correctly saved as validated
     * @returns void
     */
    public function test_add_equipment_validated_correct_values()
    {
        $user_id = $this->create_user('test');

        $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
        $response = $this->post('/equipment/enum/massUnit/add', [
            'value' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        $countEqType = EnumEquipmentType::all()->count();
        $response = $this->post('/equipment/enum/type/add', [
            'value' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        $response = $this->post('/equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_location' => 'three',
            'eq_type' => 'three',
            'eq_massUnit' => 'three',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_location' => 'three',
            'eq_type' => 'three',
            'eq_massUnit' => 'three'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => 1,
            'eqTemp_location' => 'three',
            'eqTemp_validate' => 'validated',
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_mass' => 1234,
            'eqTemp_remarks' => 'three',
            'eqTemp_mobility' => null,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'three')->first()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'three')->first()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
    }

    /**
     * Test Conception Number: 42
     * Update the data of a drafted equipment with correct values
     * Expected Result: The equipment is correctly saved and modified as drafted
     * @returns void
     */
    public function test_update_equipment_drafted_correct_values()
    {
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'other')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', 'other')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        }
        $this->create_equipment('three');
        $response = $this->post('/equipment/verif', [
            'reason' => 'update',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->last()->id,
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'other',
            'eq_externalReference' => 'other',
            'eq_name' => 'other',
            'eq_serialNumber' => 'other',
            'eq_constructor' => 'other',
            'eq_mass' => 12345,
            'eq_remarks' => 'other',
            'eq_set' => 'other',
            'eq_location' => 'other',
            'eq_type' => 'other',
            'eq_massUnit' => 'other',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/update/' . Equipment::all()->where('eq_internalReference', '=', 'three')->last()->id, [
            'reason' => 'update',
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'other',
            'eq_externalReference' => 'other',
            'eq_name' => 'other',
            'eq_serialNumber' => 'other',
            'eq_constructor' => 'other',
            'eq_mass' => 12345,
            'eq_remarks' => 'other',
            'eq_set' => 'other',
            'eq_location' => 'other',
            'eq_type' => 'other',
            'eq_massUnit' => 'other'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment', [
            'eq_internalReference' => 'other',
            'eq_externalReference' => 'other',
            'eq_name' => 'other',
            'eq_serialNumber' => 'other',
            'eq_constructor' => 'other',
            'eq_set' => 'other',
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->where('eq_internalReference', '=', 'other')->last()->id,
            'eqTemp_location' => 'other',
            'eqTemp_validate' => 'drafted',
            'eqTemp_mass' => 12345,
            'eqTemp_remarks' => 'other',
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'other')->last()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'other')->last()->id,
        ]);
    }

    public function create_equipment($name, $validated = 'drafted')
    {
        $user_id = $this->create_user('test');

        if (EnumEquipmentMassUnit::all()->where('value', '=', $name)->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => $name,
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', $name)->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => $name,
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        }
        $response = $this->post('/equipment/verif', [
            'eq_validate' => $validated,
            'eq_internalReference' => $name,
            'eq_externalReference' => $name,
            'eq_name' => $name,
            'eq_serialNumber' => $name,
            'eq_constructor' => $name,
            'eq_mass' => 1234,
            'eq_remarks' => $name,
            'eq_set' => $name,
            'eq_location' => $name,
            'eq_type' => $name,
            'eq_massUnit' => $name,
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response = $this->post('/equipment/add', [
            'eq_validate' => $validated,
            'eq_internalReference' => $name,
            'eq_externalReference' => $name,
            'eq_name' => $name,
            'eq_serialNumber' => $name,
            'eq_constructor' => $name,
            'eq_mass' => 1234,
            'eq_remarks' => $name,
            'eq_set' => $name,
            'eq_location' => $name,
            'eq_type' => $name,
            'eq_massUnit' => $name,
            'eq_mobility' => '0'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => '1',
            'eqTemp_location' => $name,
            'eqTemp_validate' => $validated,
            'eqTemp_lifeSheetCreated' => '0',
            'eqTemp_mass' => '1234',
            'eqTemp_remarks' => $name,
            'eqTemp_mobility' => '0',
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', $name)->first()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', $name)->first()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
        return Equipment::all()->last()->id;
    }

    /**
     * Test Conception Number: 43
     * Update the data of a drafted equipment with existent values
     * Expected Result: Receiving an error :
     *                                          "This internal reference is already use for another equipment"
     * @returns void
     */
    public function test_update_equipment_drafted_existent_values()
    {
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'other')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', 'other')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        }
        $this->create_equipment('three');
        $this->create_equipment('Exist');
        $response = $this->post('/equipment/verif', [
            'reason' => 'update',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->last()->id,
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'Exist',
            'eq_externalReference' => 'Exist',
            'eq_name' => 'Exist',
            'eq_serialNumber' => 'Exist',
            'eq_constructor' => 'Exist',
            'eq_mass' => 1234,
            'eq_remarks' => 'Exist',
            'eq_set' => 'Exist',
            'eq_location' => 'Exist',
            'eq_type' => 'Exist',
            'eq_massUnit' => 'Exist',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'eq_internalReference' => 'This internal reference is already use for another equipment'
        ]);
    }

    /**
     * Test Conception Number: 44
     * Update the data of a to be validated equipment with correct values
     * Expected Result: The equipment is correctly saved as to_be_validated and modified as drafted
     * @returns void
     */
    public function test_update_equipment_toBeValidated_correct_values()
    {
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'other')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', 'other')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        }
        $this->create_equipment('three', 'to_be_validated');
        $response = $this->post('/equipment/verif', [
            'reason' => 'update',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->last()->id,
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'other',
            'eq_externalReference' => 'other',
            'eq_name' => 'other',
            'eq_serialNumber' => 'other',
            'eq_constructor' => 'other',
            'eq_mass' => 12345,
            'eq_remarks' => 'other',
            'eq_set' => 'other',
            'eq_location' => 'other',
            'eq_type' => 'other',
            'eq_massUnit' => 'other',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/update/' . Equipment::all()->where('eq_internalReference', '=', 'three')->last()->id, [
            'reason' => 'update',
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'other',
            'eq_externalReference' => 'other',
            'eq_name' => 'other',
            'eq_serialNumber' => 'other',
            'eq_constructor' => 'other',
            'eq_mass' => 12345,
            'eq_remarks' => 'other',
            'eq_set' => 'other',
            'eq_location' => 'other',
            'eq_type' => 'other',
            'eq_massUnit' => 'other'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment', [
            'eq_internalReference' => 'other',
            'eq_externalReference' => 'other',
            'eq_name' => 'other',
            'eq_serialNumber' => 'other',
            'eq_constructor' => 'other',
            'eq_set' => 'other',
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->where('eq_internalReference', '=', 'other')->last()->id,
            'eqTemp_location' => 'other',
            'eqTemp_validate' => 'drafted',
            'eqTemp_mass' => 12345,
            'eqTemp_remarks' => 'other',
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'other')->last()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'other')->last()->id,
        ]);
    }

    /**
     * Test Conception Number: 45
     * Update the data of a to be validated equipment with existent values
     * Expected Result: Receiving an error :
     *                                          "This internal reference is already use for another equipment"
     * @returns void
     */
    public function test_update_equipment_toBeValidated_existent_values()
    {
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'other')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', 'other')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        }
        $this->create_equipment('three', 'to_be_validated');
        $this->create_equipment('Exist', 'to_be_validated');
        $response = $this->post('/equipment/verif', [
            'reason' => 'update',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->last()->id,
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'Exist',
            'eq_externalReference' => 'Exist',
            'eq_name' => 'Exist',
            'eq_serialNumber' => 'Exist',
            'eq_constructor' => 'Exist',
            'eq_mass' => 1234,
            'eq_remarks' => 'Exist',
            'eq_set' => 'Exist',
            'eq_location' => 'Exist',
            'eq_type' => 'Exist',
            'eq_massUnit' => 'Exist',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'eq_internalReference' => 'This internal reference is already use for another equipment'
        ]);
    }

    /**
     * Test Conception Number: 46
     * Update the internal reference of a validated equipment with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the internal reference because you have already validated the id card"
     * @returns void
     */
    public function test_update_internal_reference_equipment_validated()
    {
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'other')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', 'other')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        }
        $this->create_equipment('three', 'validated');
        $response = $this->post('/equipment/verif', [
            'reason' => 'update',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->last()->id,
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'other',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_location' => 'three',
            'eq_type' => 'three',
            'eq_massUnit' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'eq_internalReference' => 'You can\'t modify the internal reference because you have already validated the id card'
        ]);
    }

    /**
     * Test Conception Number: 47
     * Update the external reference of a validated equipment with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the external reference because you have already validated the id card"
     * @returns void
     */
    public function test_update_external_reference_equipment_validated()
    {
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'other')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', 'other')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        }
        $this->create_equipment('three', 'validated');
        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/verif', [
            'reason' => 'update',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->last()->id,
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'other',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_location' => 'three',
            'eq_type' => 'three',
            'eq_massUnit' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'eq_externalReference' => 'You can\'t modify the external reference because you have already validated the id card'
        ]);
    }

    /**
     * Test Conception Number: 48
     * Update the name of a validated equipment with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the name because you have already validated the id card"
     * @returns void
     */
    public function test_update_name_equipment_validated()
    {
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'other')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', 'other')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        }
        $this->create_equipment('three', 'validated');
        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/verif', [
            'reason' => 'update',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->last()->id,
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'other',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_location' => 'three',
            'eq_type' => 'three',
            'eq_massUnit' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'eq_name' => 'You can\'t modify the name because a life sheet has already been created'
        ]);
    }

    /**
     * Test Conception Number: 49
     * Update the serial number of a validated equipment with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the serial number because you have already validated the id card"
     * @returns void
     */
    public function test_update_serial_number_equipment_validated()
    {
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'other')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', 'other')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        }
        $this->create_equipment('three', 'validated');
        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/verif', [
            'reason' => 'update',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->last()->id,
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'other',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_location' => 'three',
            'eq_type' => 'three',
            'eq_massUnit' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'eq_serialNumber' => 'You can\'t modify the serial number because a life sheet has already been created'
        ]);
    }

    /**
     * Test Conception Number: 50
     * Update the constructor of a validated equipment with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the constructor because you have already validated the id card"
     * @returns void
     */
    public function test_update_constructor_equipment_validated()
    {
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'other')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', 'other')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        }
        $this->create_equipment('three', 'validated');
        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/verif', [
            'reason' => 'update',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->last()->id,
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'other',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_location' => 'three',
            'eq_type' => 'three',
            'eq_massUnit' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'eq_constructor' => 'You can\'t modify the constructor because a life sheet has already been created'
        ]);
    }

    /**
     * Test Conception Number: 51
     * Update the set of a validated equipment with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the set because you have already validated the id card"
     * @returns void
     */
    public function test_update_set_equipment_validated()
    {
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'other')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', 'other')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        }
        $this->create_equipment('three', 'validated');
        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/verif', [
            'reason' => 'update',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->last()->id,
            'eq_validate' => 'validated',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'other',
            'eq_location' => 'three',
            'eq_type' => 'three',
            'eq_massUnit' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'eq_set' => 'You can\'t modify the set because a life sheet has already been created'
        ]);
    }

    /**
     * Test Conception Number: 52
     * Update a signed equipment with correct values
     * Expected Result: The equipment is correctly saved as validated and updated in the database
     * @returns void
     */
    public function test_update_value_equipment_signed()
    {
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'other')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', 'other')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'other',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());
        }
        $this->create_equipment('three', 'validated');
        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id
        ]);
        $oldVersion = Equipment::all()->where('eq_internalReference', '=', 'three')->last()->eq_nbrVersion;
        $response = $this->post('/equipment/verif', [
            'reason' => 'update',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->last()->id,
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 12345,
            'eq_remarks' => 'other',
            'eq_set' => 'three',
            'eq_location' => 'other',
            'eq_type' => 'other',
            'eq_massUnit' => 'other',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/update/' . Equipment::all()->where('eq_internalReference', '=', 'three')->last()->id, [
            'reason' => 'update',
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 12345,
            'eq_remarks' => 'other',
            'eq_set' => 'three',
            'eq_location' => 'other',
            'eq_type' => 'other',
            'eq_massUnit' => 'other'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment', [
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_set' => 'three',
            'eq_nbrVersion' => $oldVersion + 1
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->where('eq_internalReference', '=', 'three')->last()->id,
            'eqTemp_location' => 'other',
            'eqTemp_validate' => 'drafted',
            'eqTemp_mass' => 12345,
            'eqTemp_remarks' => 'other',
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', 'other')->last()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', 'other')->last()->id,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null
        ]);
    }

    /**
     * Test Conception Number: 53
     * Send the equipment list for the list page
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_send_values_for_list()
    {
        $this->create_equipment('three');
        $response = $this->get('/equipment/equipments');
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'id' => Equipment::all()->last()->id,
                'eq_internalReference' => 'three',
                'eq_externalReference' => 'three',
                'eq_name' => 'three',
                'eq_state' => 'Waiting_for_referencing',
                'state_id' => DB::select('SELECT state_id FROM pivot_equipment_temp_state WHERE equipmentTemp_id = ' . EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->last()->id)[0]->state_id,
                'eqTemp_lifeSheetCreated' => 0,
                'alreadyValidatedQuality' => false,
                'alreadyValidatedTechnical' => false,
                'eq_version' => 1,
                'needToBeRealized' => false,
                'needToBeApprove' => false,
                'validated' => 'drafted',
                'signed' => false,
                'signatureDate' => null,
            ]
        ]);
    }

    /**
     * Test Conception Number: 54
     * Send the equipment list for the list page with signed equipment
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_send_values_for_list_signed()
    {
        $this->create_equipment('three', 'validated');
        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $mostRecentlyEqTmp = EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->last();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyState->created_at;
            if ($date >= $date2) {
                $mostRecentlyState = $state;
            }
        }
        $response = $this->get('/equipment/equipments');
        $response->assertStatus(200);
        $response->assertJson([
                '0' => [
                    'id' => Equipment::all()->last()->id,
                    'eq_internalReference' => 'three',
                    'eq_externalReference' => 'three',
                    'eq_name' => 'three',
                    'eq_state' => 'In_use',
                    'state_id' => $mostRecentlyState->id,
                    'eqTemp_lifeSheetCreated' => 1,
                    'alreadyValidatedQuality' => true,
                    'alreadyValidatedTechnical' => true,
                    'eq_version' => 1,
                    'needToBeRealized' => false,
                    'needToBeApprove' => false,
                    'validated' => 'validated',
                    'signed' => true,
                ]
            ]
        );
    }

    /**
     * Test Conception Number: 55
     * Send the equipment list from the set
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_send_values_from_set()
    {
        $this->create_equipment('three');
        $response = $this->get('/equipments/same_set/three');
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'id' => Equipment::all()->last()->id,
                'eq_internalReference' => 'three',
                'eq_externalReference' => 'three',
                'eq_name' => 'three',
                'eq_serialNumber' => 'three',
                'eq_constructor' => 'three',
                'eq_set' => 'three',
                'eq_nbrVersion' => 1,
                'state_id' => null,
            ]
        ]);
    }

    /**
     * Test Conception Number: 56
     * Send the equipment list from the id
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_send_values_from_id()
    {
        $this->create_equipment('three');
        $response = $this->get('/equipment/' . Equipment::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_type' => 'three',
            'eq_version' => '01',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => '1234',
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_massUnit' => 'three',
            'eq_mobility' => false,
            'eq_validate' => 'drafted',
            'eq_lifeSheetCreated' => 0,
            'eq_technicalVerifier_firstName' => NULL,
            'eq_technicalVerifier_lastName' => NULL,
            'eq_qualityVerifier_firstName' => NULL,
            'eq_qualityVerifier_lastName' => NULL,
            'eq_location' => 'three',
            'eq_signatureDate' => NULL,
        ]);
    }

    /**
     * Test Conception Number: 57
     * Send a signed equipment list from the id
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_send_values_from_id_signed()
    {
        $this->create_equipment('three', 'validated');
        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->get('/equipment/' . Equipment::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_type' => 'three',
            'eq_version' => '01',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => '1234',
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_massUnit' => 'three',
            'eq_mobility' => false,
            'eq_validate' => 'validated',
            'eq_lifeSheetCreated' => 1,
            'eq_technicalVerifier_firstName' => 'test',
            'eq_technicalVerifier_lastName' => 'test',
            'eq_qualityVerifier_firstName' => 'test',
            'eq_qualityVerifier_lastName' => 'test',
            'eq_location' => 'three',
        ]);
    }

    /**
     * Test Conception Number: 58
     * Send the set list
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_send_sets()
    {
        $this->create_equipment('three');
        $response = $this->get('/equipment/sets');
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'eq_set' => 'three',
            ]
        ]);
    }

    /**
     * Test Conception Number: 59
     * Send the list for annual planning
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_send_annual_planning()
    {
        $this->create_equipment('three', 'validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => false,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
        ]);

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '72hours',
            'prvMtnOp_protocol' => '72hours',
            'prvMtnOp_periodicity' => 72,
            'prvMtnOp_symbolPeriodicity' => 'H',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '72hours',
            'prvMtnOp_protocol' => '72hours',
            'prvMtnOp_periodicity' => 72,
            'prvMtnOp_symbolPeriodicity' => 'H',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => '72hours',
            'prvMtnOp_protocol' => '72hours',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 72,
            'prvMtnOp_symbolPeriodicity' => 'H',
            'prvMtnOp_nextDate' => Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addHours(72)->format('Y-m-d H:i:s')
        ]);

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '7days',
            'prvMtnOp_protocol' => '7days',
            'prvMtnOp_periodicity' => 7,
            'prvMtnOp_symbolPeriodicity' => 'D',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '7days',
            'prvMtnOp_protocol' => '7days',
            'prvMtnOp_periodicity' => 7,
            'prvMtnOp_symbolPeriodicity' => 'D',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => '7days',
            'prvMtnOp_protocol' => '7days',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 7,
            'prvMtnOp_symbolPeriodicity' => 'D',
            'prvMtnOp_nextDate' => Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addDays(7)->format('Y-m-d H:i:s')
        ]);

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '1month',
            'prvMtnOp_protocol' => '1month',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '1month',
            'prvMtnOp_protocol' => '1month',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => '1month',
            'prvMtnOp_protocol' => '1month',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_nextDate' => Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addMonth()->format('Y-m-d H:i:s')
        ]);

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '1year',
            'prvMtnOp_protocol' => '1year',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'Y',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '1year',
            'prvMtnOp_protocol' => '1year',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'Y',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => '1year',
            'prvMtnOp_protocol' => '1year',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'Y',
            'prvMtnOp_nextDate' => Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addYear()->format('Y-m-d H:i:s')
        ]);

        $response = $this->get('/equipment/prvMtnOp/planning');
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'id' => Equipment::all()->last()->id,
                'internalReference' => 'three',
                'name' => 'three',
                'preventive_maintenance_operations' => [
                    '0' => [
                        'id' => PreventiveMaintenanceOperation::all()->where('prvMtnOp_description', 'three')->last()->id,
                        'prvMtnOp_number' => '1',
                        'prvMtnOp_description' => 'three',
                        'prvMtnOp_periodicity' => 'N/A',
                        'prvMtnOp_symbolPeriodicity' => '',
                    ],
                    '1' => [
                        'id' => PreventiveMaintenanceOperation::all()->where('prvMtnOp_description', '72hours')->last()->id,
                        'prvMtnOp_number' => '2',
                        'prvMtnOp_description' => '72hours',
                        'prvMtnOp_periodicity' => '72',
                        'prvMtnOp_symbolPeriodicity' => 'H',
                    ],
                    '2' => [
                        'id' => PreventiveMaintenanceOperation::all()->where('prvMtnOp_description', '7days')->last()->id,
                        'prvMtnOp_number' => '3',
                        'prvMtnOp_description' => '7days',
                        'prvMtnOp_periodicity' => '7',
                        'prvMtnOp_symbolPeriodicity' => 'D',
                    ],
                    '3' => [
                        'id' => PreventiveMaintenanceOperation::all()->where('prvMtnOp_description', '1month')->last()->id,
                        'prvMtnOp_number' => '4',
                        'prvMtnOp_description' => '1month',
                        'prvMtnOp_periodicity' => '1',
                        'prvMtnOp_symbolPeriodicity' => 'M',
                    ],
                    '4' => [
                        'id' => PreventiveMaintenanceOperation::all()->where('prvMtnOp_description', '1year')->last()->id,
                        'prvMtnOp_number' => '5',
                        'prvMtnOp_description' => '1year',
                        'prvMtnOp_periodicity' => '1',
                        'prvMtnOp_symbolPeriodicity' => 'Y',
                    ]
                ]
            ]
        ]);
    }

    /**
     * Test Conception Number: 60
     * Send the list for monthly planning
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_send_monthly_planning()
    {
        $this->create_equipment('three', 'validated');

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_preventiveOperation' => false,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => false,
            'prvMtnOp_description' => 'three',
            'prvMtnOp_protocol' => 'three',
            'prvMtnOp_validate' => 'validated',
        ]);

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '72hours',
            'prvMtnOp_protocol' => '72hours',
            'prvMtnOp_periodicity' => 72,
            'prvMtnOp_symbolPeriodicity' => 'H',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '72hours',
            'prvMtnOp_protocol' => '72hours',
            'prvMtnOp_periodicity' => 72,
            'prvMtnOp_symbolPeriodicity' => 'H',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => '72hours',
            'prvMtnOp_protocol' => '72hours',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 72,
            'prvMtnOp_symbolPeriodicity' => 'H',
            'prvMtnOp_nextDate' => Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addHours(72)->format('Y-m-d H:i:s')
        ]);

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '7days',
            'prvMtnOp_protocol' => '7days',
            'prvMtnOp_periodicity' => 7,
            'prvMtnOp_symbolPeriodicity' => 'D',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '7days',
            'prvMtnOp_protocol' => '7days',
            'prvMtnOp_periodicity' => 7,
            'prvMtnOp_symbolPeriodicity' => 'D',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => '7days',
            'prvMtnOp_protocol' => '7days',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 7,
            'prvMtnOp_symbolPeriodicity' => 'D',
            'prvMtnOp_nextDate' => Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addDays(7)->format('Y-m-d H:i:s')
        ]);

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '1month',
            'prvMtnOp_protocol' => '1month',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '1month',
            'prvMtnOp_protocol' => '1month',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => '1month',
            'prvMtnOp_protocol' => '1month',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'M',
            'prvMtnOp_nextDate' => Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addMonth()->format('Y-m-d H:i:s')
        ]);

        $response = $this->post('/prvMtnOp/verif', [
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_description' => '1year',
            'prvMtnOp_protocol' => '1year',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'Y',
            'prvMtnOp_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/prvMtnOp', [
            'prvMtnOp_validate' => 'validated',
            'eq_id' => Equipment::all()->last()->id,
            'prvMtnOp_description' => '1year',
            'prvMtnOp_protocol' => '1year',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'Y',
            'prvMtnOp_preventiveOperation' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('preventive_maintenance_operations', [
            'prvMtnOp_preventiveOperation' => true,
            'prvMtnOp_description' => '1year',
            'prvMtnOp_protocol' => '1year',
            'prvMtnOp_validate' => 'validated',
            'prvMtnOp_periodicity' => 1,
            'prvMtnOp_symbolPeriodicity' => 'Y',
            'prvMtnOp_nextDate' => Carbon::create(PreventiveMaintenanceOperation::all()->last()->prvMtnOp_startDate)->addYear()->format('Y-m-d H:i:s')
        ]);

        PreventiveMaintenanceOperation::all()->last()->update([
            'prvMtnOp_startDate' => Carbon::now()->subYears(2)->format('Y-m-d H:i:s'),
            'prvMtnOp_nextDate' => Carbon::now()->subYear()->format('Y-m-d H:i:s')
        ]);

        $response = $this->get('/equipment/prvMtnOp/planning_monthly');
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'id' => PreventiveMaintenanceOperation::all()->where('prvMtnOp_description', '72hours')->last()->id,
                'Number' => '2',
                'Description' => '72hours',
                'prvMtnOp_periodicity' => '72',
                'prvMtnOp_symbolPeriodicity' => 'H',
                'nextDate' => Carbon::create(PreventiveMaintenanceOperation::all()->where('prvMtnOp_description', '72hours')->last()->prvMtnOp_startDate)->addHours(72)->format('d m Y'),
                'Protocol' => '72hours',
                'Internal_Ref' => 'three',
                'Name' => 'three',
                'eq_id' => Equipment::all()->last()->id,
            ],
            '1' => [
                'id' => PreventiveMaintenanceOperation::all()->where('prvMtnOp_description', '7days')->last()->id,
                'Number' => '3',
                'Description' => '7days',
                'prvMtnOp_periodicity' => '7',
                'prvMtnOp_symbolPeriodicity' => 'D',
                'nextDate' => Carbon::create(PreventiveMaintenanceOperation::all()->where('prvMtnOp_description', '7days')->last()->prvMtnOp_startDate)->addDays(7)->format('d m Y'),
                'Protocol' => '7days',
                'Internal_Ref' => 'three',
                'Name' => 'three',
                'eq_id' => Equipment::all()->last()->id,
            ],
            '2' => [
                'id' => PreventiveMaintenanceOperation::all()->where('prvMtnOp_description', '1month')->last()->id,
                'Number' => '4',
                'Description' => '1month',
                'prvMtnOp_periodicity' => '1',
                'prvMtnOp_symbolPeriodicity' => 'M',
                'nextDate' => Carbon::create(PreventiveMaintenanceOperation::all()->where('prvMtnOp_description', '1month')->last()->prvMtnOp_startDate)->addMonth()->format('d m Y'),
                'Protocol' => '1month',
                'Internal_Ref' => 'three',
                'Name' => 'three',
                'eq_id' => Equipment::all()->last()->id,
            ],
        ]);
    }

    /**
     * Test Conception Number: 61
     * Try to add two equipment with the same values
     * Expected Result: Receiving an error:
     *                                      "This internal reference is already use for another equipment"
     * @returns void
     */
    public function test_add_same_values()
    {
        $this->create_equipment('three');
        $response = $this->post('/equipment/verif', [
            'reason' => 'add',
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'three',
            'eq_externalReference' => 'three',
            'eq_name' => 'three',
            'eq_serialNumber' => 'three',
            'eq_constructor' => 'three',
            'eq_mass' => 1234,
            'eq_remarks' => 'three',
            'eq_set' => 'three',
            'eq_location' => 'three',
            'eq_type' => 'three',
            'eq_massUnit' => 'three',
            'createdBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'eq_internalReference' => 'This internal reference is already use for another equipment'
        ]);
    }

    /**
     * Test Conception Number: 62
     * Send the periode data for the annual planning
     * Expected Result: The data are correctly send
     * @returns void
     */
    public function test_send_periode_data()
    {
        $this->create_equipment('three');
        $response = $this->get('/send/equipment/planning/periode');
        $response->assertStatus(200);
        $firstOfMonth = Carbon::create(Carbon::now()->format('Y-m-01'));
        $response->assertJson([
            '0' => [
                'id' => $firstOfMonth->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '1' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '2' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '3' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '4' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '5' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '6' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '7' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '8' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '9' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '10' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '11' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '12' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '13' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '14' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '15' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '16' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '17' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '18' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '19' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '20' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '21' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '22' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
            '23' => [
                'id' => $firstOfMonth->addMonth()->format('m-Y'),
                'month' => $firstOfMonth->format('M'),
                'year' => (int)($firstOfMonth->format('o')),
            ],
        ]);
    }
}
