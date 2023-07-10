<?php

namespace Tests\Feature;

use App\Models\SW01\EnumEquipmentMassUnit;
use App\Models\SW01\EnumEquipmentType;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\Mme;
use App\Models\SW01\MmeState;
use App\Models\SW01\MmeTemp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MmeTest extends TestCase
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
     * Add new mme as drafted with no values
     * Internal Ref: /
     * Name: /
     * External Ref: /
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter an internal reference"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_mme_drafted_no_values()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'drafted',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_internalReference' => 'You must enter an internal reference',
            'mme_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 2
     * Add new mme as drafted with a too short internal ref
     * Internal Ref: "in"
     * Name: /
     * External Ref: /
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_mme_drafted_short_intern_ref()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_internalReference' => 'You must enter at least 3 characters',
            'mme_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 3
     * Add a new mme as drafted with a too long internal ref
     * Internal Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Name: /
     * External Ref: /
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 16 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_mme_drafted_long_intern_ref()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_internalReference' => 'You must enter a maximum of 16 characters',
            'mme_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 4
     * Add a new mme as drafted with a too long name
     * Internal Ref: "three"
     * Name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor."
     * External Ref: /
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_mme_drafted_long_name()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'three',
            'mme_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_name' => 'You must enter a maximum of 100 characters',
            'mme_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 5
     * Add a new mme as drafted with a too short external ref
     * Internal Ref: "three"
     * Name: /
     * External Ref: "in"
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     * @returns void
     */
    public function test_add_mme_drafted_short_alpha_ref()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_externalReference' => 'You must enter at least 3 characters'
        ]);
    }

    /**
     * Test Conception Number: 6
     * Add a new mme as drafted with a too long external ref
     * Internal Ref: "three"
     * Name: /
     * External Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor."
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     * @returns void
     */
    public function test_add_mme_drafted_long_alpha_ref()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_externalReference' => 'You must enter a maximum of 100 characters'
        ]);
    }

    /**
     * Test Conception Number: 7
     * Add a new mme as drafted with a too long serial number
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Serial Number: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_mme_drafted_long_serial_number()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_serialNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_serialNumber' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 8
     * Add a new mme as drafted with a too long constructor
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Serial Number: /
     * Constructor: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 30 characters"
     * @returns void
     */
    public function test_add_mme_drafted_long_constructor()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_constructor' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_constructor' => 'You must enter a maximum of 30 characters'
        ]);
    }

    /**
     * Test Conception Number: 10
     * Add a new mme as drafted with a too long set
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Serial Number: /
     * Constructor: /
     * Unit:
     * Mobil ? : /
     * Remarks: /
     * Set: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 20 characters"
     * @returns void
     */
    public function test_add_mme_drafted_long_set()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_set' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_set' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 11
     * Successfully saved a new mme as drafted
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: The mme is saved as drafted and successfully added in the database
     * @returns void
     */
    public function test_add_mme_drafted_success()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add', [
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three'
        ]);
        $this->assertDatabaseHas('mmes', [
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three'
        ]);
    }

    /**
     * Test Conception Number: 12
     * Add a new mme as to be validated with no values
     * Internal Ref: /
     * Name: /
     * External Ref: /
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter an internal reference"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_mme_to_be_validated_no_values()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'to_be_validated',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_internalReference' => 'You must enter an internal reference',
            'mme_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 13
     * Add a new mme as to be validated with a too short internal reference
     * Internal Ref: "in"
     * Name: /
     * External Ref: /
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_mme_to_be_validated_short_internal_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'to_be_validated',
            'mme_internalReference' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_internalReference' => 'You must enter at least 3 characters',
            'mme_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 14
     * Add a new mme as to be validated with a too long internal reference
     * Internal Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Name: /
     * External Ref: /
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 16 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_mme_to_be_validated_long_internal_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'to_be_validated',
            'mme_internalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_internalReference' => 'You must enter a maximum of 16 characters',
            'mme_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 15
     * Add a new mme as to be validated with a too long name
     * Internal Ref: "three"
     * Name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. "
     * External Ref: /
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     *                                      "You must enter an external reference"
     * @returns void
     */
    public function test_add_mme_to_be_validated_long_name()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'to_be_validated',
            'mme_internalReference' => 'three',
            'mme_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_name' => 'You must enter a maximum of 100 characters',
            'mme_externalReference' => 'You must enter an external reference'
        ]);
    }

    /**
     * Test Conception Number: 16
     * Add a new mme as to be validated with a too short external reference
     * Internal Ref: "three"
     * Name: /
     * External Ref: "in"
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     * @returns void
     */
    public function test_add_mme_to_be_validated_short_external_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'to_be_validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_externalReference' => 'You must enter at least 3 characters'
        ]);
    }

    /**
     * Test Conception Number: 17
     * Add a new mme as to be validated with a too long external reference
     * Internal Ref: "three"
     * Name: /
     * External Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. "
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     * @returns void
     */
    public function test_add_mme_to_be_validated_long_external_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'to_be_validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_externalReference' => 'You must enter a maximum of 100 characters'
        ]);
    }

    /**
     * Test Conception Number: 18
     * Add a new mme as to be validated with a too long serial number
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Serial Number: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_mme_to_be_validated_long_serial_number()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'to_be_validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_serialNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_serialNumber' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 19
     * Add a new mme as to be validated with a too long constructor
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Serial Number: /
     * Constructor: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_mme_to_be_validated_long_constructor()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'to_be_validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_constructor' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_constructor' => 'You must enter a maximum of 30 characters'
        ]);
    }

    /**
     * Test Conception Number: 21
     * Add new mme as to be validated with a too long set
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 20 characters"
     * @returns void
     */
    public function test_add_mme_to_be_validated_long_set()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'to_be_validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_set' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_set' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 22
     * Successfully add new mme as to be validated and check if it is in the database
     * Internal Ref: "three"
     * Name: /
     * External Ref: "three"
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: The mme is in the database
     * @returns void
     */
    public function test_add_mme_to_be_validated_success()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'to_be_validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add', [
            'mme_validate' => 'to_be_validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three'
        ]);
        $this->assertDatabaseHas('mmes', [
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three'
        ]);
    }

    /**
     * Test Conception Number: 23
     * Add a new mme as validated with no values
     * Internal Ref: /
     * Name: /
     * External Ref: /
     * Serial Number: /
     * Constructor: /
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
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_mme_validated_no_values()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_internalReference' => 'You must enter an internal reference',
            'mme_externalReference' => 'You must enter an external reference',
            'mme_name' => 'You must enter a name',
            'mme_serialNumber' => 'You must enter a serial number',
            'mme_constructor' => 'You must enter a constructor',
            'mme_remarks' => 'You must enter a remark',
            'mme_set' => 'You must enter a set',
            'mme_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 24
     * Add a new mme as validated with a too short internal reference
     * Internal Ref: "in"
     * Name: /
     * External Ref: /
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 characters"
     *                                      "You must enter an external reference"
     *                                      "You must enter a name"
     *                                      "You must enter a serial number"
     *                                      "You must enter a constructor"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_mme_validated_short_internal_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_internalReference' => 'You must enter at least 3 characters',
            'mme_externalReference' => 'You must enter an external reference',
            'mme_name' => 'You must enter a name',
            'mme_serialNumber' => 'You must enter a serial number',
            'mme_constructor' => 'You must enter a constructor',
            'mme_remarks' => 'You must enter a remark',
            'mme_set' => 'You must enter a set',
            'mme_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 25
     * Add a new mme as validated with a too long internal reference
     * Internal Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Name: /
     * External Ref: /
     * Serial Number: /
     * Constructor: /
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
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_mme_validated_long_internal_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_internalReference' => 'You must enter a maximum of 16 characters',
            'mme_externalReference' => 'You must enter an external reference',
            'mme_name' => 'You must enter a name',
            'mme_serialNumber' => 'You must enter a serial number',
            'mme_constructor' => 'You must enter a constructor',
            'mme_remarks' => 'You must enter a remark',
            'mme_set' => 'You must enter a set',
            'mme_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 26
     * Add a new mme as validated with a too short external reference
     * Internal Ref: "three"
     * Name: /
     * External Ref: "in"
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 caracters"
     *                                      "You must enter a name"
     *                                      "You must enter a serial number"
     *                                      "You must enter a constructor"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_mme_validated_short_external_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_externalReference' => 'You must enter at least 3 characters',
            'mme_name' => 'You must enter a name',
            'mme_serialNumber' => 'You must enter a serial number',
            'mme_constructor' => 'You must enter a constructor',
            'mme_remarks' => 'You must enter a remark',
            'mme_set' => 'You must enter a set',
            'mme_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 27
     * Add a new mme as validated with a too long external reference
     * Internal Ref: "three"
     * Name: /
     * External Ref: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     *                                      "You must enter a name"
     *                                      "You must enter a serial number"
     *                                      "You must enter a constructor"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_mme_validated_long_external_reference()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_externalReference' => 'You must enter a maximum of 100 characters',
            'mme_name' => 'You must enter a name',
            'mme_serialNumber' => 'You must enter a serial number',
            'mme_constructor' => 'You must enter a constructor',
            'mme_remarks' => 'You must enter a remark',
            'mme_set' => 'You must enter a set',
            'mme_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 28
     * Add a new mme as validated with a too short name
     * Internal Ref: "three"
     * Name: "in"
     * External Ref: "three"
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 caracters"
     *                                      "You must enter a serial number"
     *                                      "You must enter a constructor"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_mme_validated_short_name()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_name' => 'You must enter at least 3 characters',
            'mme_serialNumber' => 'You must enter a serial number',
            'mme_constructor' => 'You must enter a constructor',
            'mme_remarks' => 'You must enter a remark',
            'mme_set' => 'You must enter a set',
            'mme_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 29
     * Add a new mme as validated with a too long name
     * Internal Ref: "three"
     * Name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * External Ref: "three"
     * Serial Number: /
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 100 characters"
     *                                      "You must enter a serial number"
     *                                      "You must enter a constructor"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_mme_validated_long_name()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_name' => 'You must enter a maximum of 100 characters',
            'mme_serialNumber' => 'You must enter a serial number',
            'mme_constructor' => 'You must enter a constructor',
            'mme_remarks' => 'You must enter a remark',
            'mme_set' => 'You must enter a set',
            'mme_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 30
     * Add a new mme as validated with a too short serial number
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Serial Number: "in"
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 caracters"
     *                                      "You must enter a constructor"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_mme_validated_short_serial_number()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_serialNumber' => 'You must enter at least 3 characters',
            'mme_constructor' => 'You must enter a constructor',
            'mme_remarks' => 'You must enter a remark',
            'mme_set' => 'You must enter a set',
            'mme_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 31
     * Add a new mme as validated with a too long serial number
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Serial Number: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Constructor: /
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     *                                      "You must enter a constructor"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_mme_validated_long_serial_number()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_serialNumber' => 'You must enter a maximum of 50 characters',
            'mme_constructor' => 'You must enter a constructor',
            'mme_remarks' => 'You must enter a remark',
            'mme_set' => 'You must enter a set',
            'mme_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 32
     * Add a new mme as validated with a too short constructor
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Serial Number: "three"
     * Constructor: "in"
     * Unit: /
     * Mobil ? : /
     * Remarks: /
     * Set: /
     * Expected Result: Receiving an error:
     *                                      "You must enter at least 3 caracters"
     *                                      "You must enter a remark"
     *                                      "You must enter a set"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_mme_validated_short_constructor()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_constructor' => 'You must enter at least 3 characters',
            'mme_remarks' => 'You must enter a remark',
            'mme_set' => 'You must enter a set',
            'mme_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 33
     * Add a new mme as validated with a too long constructor
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Serial Number: "three"
     * Constructor: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
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
    public function test_add_mme_validated_long_constructor()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_constructor' => 'You must enter a maximum of 30 characters',
            'mme_remarks' => 'You must enter a remark',
            'mme_set' => 'You must enter a set',
            'mme_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 35
     * Add a new mme as validated with a too short remark
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Serial Number: "three"
     * Constructor: "three"
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
    public function test_add_mme_validated_short_remark()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'in',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_remarks' => 'You must enter at least 3 characters',
            'mme_set' => 'You must enter a set',
            'mme_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 36
     * Add a new mme as validated with a too long remark
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Serial Number: "three"
     * Constructor: "three"
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
    public function test_add_mme_validated_long_remark()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_remarks' => 'You must enter a maximum of 400 characters',
            'mme_set' => 'You must enter a set',
            'mme_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 37
     * Add a new mme as validated with a too long set
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Serial Number: "three"
     * Constructor: "three"
     * Unit: /
     * Mobil ? : /
     * Remarks: "three"
     * Set: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 50 characters"
     *                                      "You must enter a location"
     * @returns void
     */
    public function test_add_mme_validated_long_set()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_set' => 'You must enter a maximum of 50 characters',
            'mme_location' => 'You must enter a location'
        ]);
    }

    /**
     * Test Conception Number: 39
     * Add a new mme as validated with a too long location
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Serial Number: "three"
     * Constructor: "three"
     * Unit: /
     * Mobil ? : /
     * Remarks: "three"
     * Set: "three"
     * Location: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_mme_validated_long_location()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'mme_location' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 40
     * Add a new mme as validated with an existent internal reference
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Serial Number: "three"
     * Constructor: "three"
     * Unit: /
     * Mobil ? : /
     * Remarks: "three"
     * Set: "three"
     * Location: "three"
     * Expected Result: Receiving an error:
     *                                      "This internal reference is already use for another mme"
     * @returns void
     */
    public function test_add_mme_validated_existent_ref()
    {
        $this->create_mme('three');
        $response = $this->post('/mme/verif', [
            'reason' => 'add',
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'mme_internalReference' => 'This internal reference is already use for another mme',
        ]);
    }

    public function create_mme($name, $validated = 'drafted')
    {

        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => $validated,
            'mme_internalReference' => $name,
            'mme_externalReference' => $name,
            'mme_name' => $name,
            'mme_serialNumber' => $name,
            'mme_constructor' => $name,
            'mme_remarks' => $name,
            'mme_set' => $name,
            'mme_location' => $name,
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(200);
        $countEquipment = Mme::all()->count();
        $response = $this->post('/mme/add', [
            'mme_validate' => $validated,
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
        $this->assertEquals($countEquipment + 1, Mme::all()->count());
        return Mme::all()->where('mme_internalReference', '=', $name)->last()->id;
    }

    /**
     * Test Conception Number: 41
     * Add a new mme as validated with correct values
     * Internal Ref: "three"
     * Name: "three"
     * External Ref: "three"
     * Serial Number: "three"
     * Constructor: "three"
     * Unit: "three"
     * Mobil ? : /
     * Remarks: "three"
     * Set: "three"
     * Location: "three"
     * Expected Result: The mme is correctly saved as validated
     * @returns void
     */
    public function test_add_mme_validated_correct_values()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme/verif', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
            'createdBy_id' => $user_id
        ]);
        $response->assertStatus(200);
        $countEquipment = Mme::all()->count();
        $response = $this->post('/mme/add', [
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Mme::all()->count());
        $this->assertDatabaseHas('mmes', [
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_set' => 'three',
        ]);
        $this->assertDatabaseHas('mme_temps', [
            'mmeTemp_location' => 'three',
            'mmeTemp_validate' => 'validated',
            'mmeTemp_remarks' => 'three',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,

        ]);
        $this->assertEquals(1, DB::select('SELECT COUNT(id) AS cpt FROM `pivot_mme_temp_state` WHERE `mmeTemp_id`= ' . MmeTemp::all()->where('mme_id', '=', Mme::all()->last()->id)->last()->id)[0]->cpt);
    }

    /**
     * Test Conception Number: 42
     * Update the data of a drafted mme with correct values
     * Expected Result: The mme is correctly saved and modified as drafted
     * @returns void
     */
    public function test_update_mme_drafted_correct_values()
    {
        $this->create_mme('three');
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'other',
            'mme_externalReference' => 'other',
            'mme_name' => 'other',
            'mme_serialNumber' => 'other',
            'mme_constructor' => 'other',
            'mme_remarks' => 'other',
            'mme_set' => 'other',
            'mme_location' => 'other',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/update/' . Mme::all()->where('mme_internalReference', '=', 'three')->last()->id, [
            'reason' => 'update',
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'other',
            'mme_externalReference' => 'other',
            'mme_name' => 'other',
            'mme_serialNumber' => 'other',
            'mme_constructor' => 'other',
            'mme_remarks' => 'other',
            'mme_set' => 'other',
            'mme_location' => 'other',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mmes', [
            'mme_internalReference' => 'other',
            'mme_externalReference' => 'other',
            'mme_name' => 'other',
            'mme_serialNumber' => 'other',
            'mme_constructor' => 'other',
            'mme_set' => 'other',
        ]);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'other')->last()->id,
            'mmeTemp_location' => 'other',
            'mmeTemp_validate' => 'drafted',
            'mmeTemp_remarks' => 'other',
        ]);
        $this->assertEquals(1, DB::select('SELECT COUNT(id) AS cpt FROM `pivot_mme_temp_state` WHERE `mmeTemp_id`= ' . MmeTemp::all()->where('mme_id', '=', Mme::all()->last()->id)->last()->id)[0]->cpt);
    }

    /**
     * Test Conception Number: 43
     * Update the data of a drafted mme with existent values
     * Expected Result: Receiving an error :
     *                                          "This internal reference is already use for another mme"
     * @returns void
     */
    public function test_update_mme_drafted_existent_values()
    {
        $this->create_mme('three');
        $this->create_mme('Exist');
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'Exist',
            'mme_externalReference' => 'Exist',
            'mme_name' => 'Exist',
            'mme_serialNumber' => 'Exist',
            'mme_constructor' => 'Exist',
            'mme_remarks' => 'Exist',
            'mme_set' => 'Exist',
            'mme_location' => 'Exist',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'mme_internalReference' => 'This internal reference is already use for another mme'
        ]);
    }

    /**
     * Test Conception Number: 44
     * Update the data of a to be validated mme with correct values
     * Expected Result: The mme is correctly saved as to_be_validated and modified as drafted
     * @returns void
     */
    public function test_update_mme_toBeValidated_correct_values()
    {
        $this->create_mme('three', 'to_be_validated');
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'other',
            'mme_externalReference' => 'other',
            'mme_name' => 'other',
            'mme_serialNumber' => 'other',
            'mme_constructor' => 'other',
            'mme_remarks' => 'other',
            'mme_set' => 'other',
            'mme_location' => 'other',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/update/' . Mme::all()->where('mme_internalReference', '=', 'three')->last()->id, [
            'reason' => 'update',
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'other',
            'mme_externalReference' => 'other',
            'mme_name' => 'other',
            'mme_serialNumber' => 'other',
            'mme_constructor' => 'other',
            'mme_remarks' => 'other',
            'mme_set' => 'other',
            'mme_location' => 'other',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mmes', [
            'mme_internalReference' => 'other',
            'mme_externalReference' => 'other',
            'mme_name' => 'other',
            'mme_serialNumber' => 'other',
            'mme_constructor' => 'other',
            'mme_set' => 'other',
        ]);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'other')->last()->id,
            'mmeTemp_location' => 'other',
            'mmeTemp_validate' => 'drafted',
            'mmeTemp_remarks' => 'other',
        ]);
        $this->assertEquals(1, DB::select('SELECT COUNT(id) AS cpt FROM `pivot_mme_temp_state` WHERE `mmeTemp_id`= ' . MmeTemp::all()->where('mme_id', '=', Mme::all()->last()->id)->last()->id)[0]->cpt);
    }

    /**
     * Test Conception Number: 45
     * Update the data of a to be validated mme with existent values
     * Expected Result: Receiving an error :
     *                                          "This internal reference is already use for another mme"
     * @returns void
     */
    public function test_update_mme_toBeValidated_existent_values()
    {
        $this->create_mme('three', 'to_be_validated');
        $this->create_mme('Exist', 'to_be_validated');
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'Exist',
            'mme_externalReference' => 'Exist',
            'mme_name' => 'Exist',
            'mme_serialNumber' => 'Exist',
            'mme_constructor' => 'Exist',
            'mme_remarks' => 'Exist',
            'mme_set' => 'Exist',
            'mme_location' => 'Exist',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'mme_internalReference' => 'This internal reference is already use for another mme'
        ]);
    }

    /**
     * Test Conception Number: 46
     * Update the internal reference of a validated mme with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the internal reference because you have already validated the id card"
     * @returns void
     */
    public function test_update_internal_reference_mme_validated()
    {
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'other',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'mme_internalReference' => 'You can\'t modify the internal reference because you have already validated the id card'
        ]);
    }

    /**
     * Test Conception Number: 47
     * Update the external reference of a validated mme with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the external reference because you have already validated the id card"
     * @returns void
     */
    public function test_update_external_reference_mme_validated()
    {
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'other',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test Conception Number: 48
     * Update the name of a validated mme with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the name because you have already validated the id card"
     * @returns void
     */
    public function test_update_name_mme_validated()
    {
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'other',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test Conception Number: 49
     * Update the serial number of a validated mme with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the serial number because you have already validated the id card"
     * @returns void
     */
    public function test_update_serial_number_mme_validated()
    {
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'other',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test Conception Number: 50
     * Update the constructor of a validated mme with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the constructor because you have already validated the id card"
     * @returns void
     */
    public function test_update_constructor_mme_validated()
    {
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'other',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test Conception Number: 51
     * Update the set of a validated mme with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the set because you have already validated the id card"
     * @returns void
     */
    public function test_update_set_mme_validated()
    {
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'other',
            'mme_location' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test Conception Number: 52
     * Update the internal reference of a validated mme with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the internal reference because you have already validated the id card"
     * @returns void
     */
    public function test_update_internal_reference_mme_signed()
    {
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->last()->id,
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id
        ]);
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'other',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'mme_internalReference' => 'You can\'t modify the internal reference because you have already validated the id card'
        ]);
    }

    /**
     * Test Conception Number: 53
     * Update the external reference of a validated mme with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the external reference because you have already validated the id card"
     * @returns void
     */
    public function test_update_external_reference_mme_signed()
    {
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->last()->id,
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id
        ]);
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'other',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'mme_externalReference' => 'You can\'t modify the external reference because you have already validated the id card'
        ]);
    }

    /**
     * Test Conception Number: 54
     * Update the name of a validated mme with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the name because a life sheet has already been created"
     * @returns void
     */
    public function test_update_name_mme_signed()
    {
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->last()->id,
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id
        ]);
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'other',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'mme_name' => 'You can\'t modify the name because a life sheet has already been created'
        ]);
    }

    /**
     * Test Conception Number: 55
     * Update the serial number of a validated mme with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the serial number because a life sheet has already been created"
     * @returns void
     */
    public function test_update_serial_number_mme_signed()
    {
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->last()->id,
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id
        ]);
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'other',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'mme_serialNumber' => 'You can\'t modify the serial number because a life sheet has already been created'
        ]);
    }

    /**
     * Test Conception Number: 56
     * Update the constructor of a validated mme with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the constructor because a life sheet has already been created"
     * @returns void
     */
    public function test_update_constructor_mme_signed()
    {
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->last()->id,
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id
        ]);
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'other',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_location' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'mme_constructor' => 'You can\'t modify the constructor because a life sheet has already been created'
        ]);
    }

    /**
     * Test Conception Number: 57
     * Update the set of a validated mme with correct values
     * Expected Result: Receiving an error :
     *                                          "You can't modify the set because a life sheet has already been created"
     * @returns void
     */
    public function test_update_set_mme_signed()
    {
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->last()->id,
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id
        ]);
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'validated',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'other',
            'mme_location' => 'three',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'mme_set' => 'You can\'t modify the set because a life sheet has already been created'
        ]);
    }

    /**
     * Test Conception Number: 58
     * Update a signed mme with correct values
     * Expected Result: The mme is correctly saved as validated and updated in the database
     * @returns void
     */
    public function test_update_value_mme_signed()
    {
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->last()->id,
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id
        ]);
        $oldVersion = Mme::all()->where('mme_internalReference', '=', 'three')->last()->mme_nbrVersion;
        $response = $this->post('/mme/verif', [
            'reason' => 'update',
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'other',
            'mme_set' => 'three',
            'mme_location' => 'other',
            'createdBy_id' => User::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/update/' . Mme::all()->where('mme_internalReference', '=', 'three')->last()->id, [
            'reason' => 'update',
            'mme_validate' => 'drafted',
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'other',
            'mme_set' => 'three',
            'mme_location' => 'other',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mmes', [
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_set' => 'three',
            'mme_nbrVersion' => $oldVersion + 1
        ]);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
            'mmeTemp_location' => 'other',
            'mmeTemp_validate' => 'drafted',
            'mmeTemp_remarks' => 'other',
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null
        ]);
    }

    /**
     * Test Conception Number: 59
     * Send the mme list for the list page
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_send_values_for_list()
    {
        $this->create_mme('three');
        $response = $this->get('/mme/mmes');
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'id' => Mme::all()->last()->id,
                'mme_internalReference' => 'three',
                'mme_externalReference' => 'three',
                'mme_name' => 'three',
                'mme_state' => 'Waiting_for_referencing',
                'state_id' => DB::select('SELECT mme_state_id FROM pivot_mme_temp_state WHERE mmeTemp_id = ' . MmeTemp::all()->where('mme_id', '=', Mme::all()->last()->id)->last()->id)[0]->mme_state_id,
                'mmeTemp_lifeSheetCreated' => 0,
                'alreadyValidatedQuality' => false,
                'alreadyValidatedTechnical' => false,
                'mme_version' => 1,
                'needToBeRealized' => false,
                'needToBeApprove' => false,
            ]
        ]);
    }

    /**
     * Test Conception Number: 60
     * Send the mme list for the list page with signed mme
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_send_values_for_list_signed()
    {
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $mostRecentlyMmeTmp = MmeTemp::all()->where('mme_id', '=', Mme::all()->last()->id)->last();
        $states = $mostRecentlyMmeTmp->states;
        $mostRecentlyState = MmeState::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyState->created_at;
            if ($date >= $date2) {
                $mostRecentlyState = $state;
            }
        }
        $response = $this->get('/mme/mmes');
        $response->assertStatus(200);
        $response->assertJson([
                '0' => [
                    'id' => Mme::all()->last()->id,
                    'mme_internalReference' => 'three',
                    'mme_externalReference' => 'three',
                    'mme_name' => 'three',
                    'mme_state' => 'In_use',
                    'state_id' => $mostRecentlyState->id,
                    'mmeTemp_lifeSheetCreated' => 1,
                    'alreadyValidatedQuality' => true,
                    'alreadyValidatedTechnical' => true,
                    'mme_version' => 1,
                    'needToBeRealized' => false,
                    'needToBeApprove' => false,
                ]
            ]
        );
    }

    /**
     * Test Conception Number: 61
     * Send the mme list from the set
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_send_values_from_set()
    {
        $this->create_mme('three');
        $response = $this->get('/mmes/same_set/three');
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'id' => Mme::all()->last()->id,
                'mme_internalReference' => 'three',
                'mme_externalReference' => 'three',
                'mme_name' => 'three',
                'mme_serialNumber' => 'three',
                'mme_constructor' => 'three',
                'mme_set' => 'three',
                'mme_nbrVersion' => 1,
                'state_id' => null,
            ]
        ]);
    }

    /**
     * Test Conception Number: 62
     * Send the mme list from the id
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_send_values_from_id()
    {
        $this->create_mme('three');
        $response = $this->get('/mme/' . Mme::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_version' => '01',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_validate' => 'drafted',
            'mme_lifeSheetCreated' => 0,
            'mme_technicalVerifier_firstName' => NULL,
            'mme_technicalVerifier_lastName' => NULL,
            'mme_qualityVerifier_firstName' => NULL,
            'mme_qualityVerifier_lastName' => NULL,
            'mme_location' => 'three',
        ]);
    }

    /**
     * Test Conception Number: 63
     * Send a signed mme list from the id
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_send_values_from_id_signed()
    {
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->get('/mme/' . Mme::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            'mme_internalReference' => 'three',
            'mme_externalReference' => 'three',
            'mme_name' => 'three',
            'mme_version' => '01',
            'mme_serialNumber' => 'three',
            'mme_constructor' => 'three',
            'mme_remarks' => 'three',
            'mme_set' => 'three',
            'mme_validate' => 'validated',
            'mme_lifeSheetCreated' => 1,
            'mme_technicalVerifier_firstName' => 'test',
            'mme_technicalVerifier_lastName' => 'test',
            'mme_qualityVerifier_firstName' => 'test',
            'mme_qualityVerifier_lastName' => 'test',
            'mme_location' => 'three',
        ]);
    }

    /**
     * Test Conception Number: 64
     * Send the set list
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_send_sets()
    {
        $this->create_mme('three');
        $response = $this->get('/mme/sets');
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'mme_set' => 'three',
            ]
        ]);
    }

    /**
     * Test Conception Number: 65
     * Add a mme and linked it to an equipment
     * Expected Result: The mme is correctly linked to the equipment
     * @returns void
     */
    public function test_add_link_mme_to_equipment()
    {
        $eq_id = $this->create_equipment('three');
        $this->create_mme('three');
        $response = $this->post('/mme/link_to_eq/' . $eq_id, [
            'mme_internalReference' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mmes', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', $eq_id)->last()->id,
            'id' => MME::all()->where('mme_internalReference', '=', 'three')->first()->id,
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
     * Test Conception Number: 66
     * Add a mme and linked it to a signed equipment
     * Expected Result: The mme is correctly linked to the equipment and the equipment is no longer signed
     * @returns void
     */
    public function test_add_link_mme_to_equipment_signed()
    {
        $eq_id = $this->create_equipment('three', 'validated');
        $response = $this->post('/equipment/validation/' . $eq_id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . $eq_id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_lifeSheetCreated' => '1',
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id,
            'eqTemp_version' => '1',
        ]);

        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/link_to_eq/' . $eq_id, [
            'mme_internalReference' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_lifeSheetCreated' => '0',
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
            'eqTemp_version' => '2',
        ]);
        $this->assertDatabaseHas('mmes', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', $eq_id)->last()->id,
            'id' => MME::all()->where('mme_internalReference', '=', 'three')->first()->id,
        ]);
    }

    /**
     * Test Conception Number: 67
     * Unlinked a mme from equipment
     * Expected Result: The mme is correctly unlinked from the equipment
     * @returns void
     */
    public function test_add_unlink_mme_to_equipment()
    {
        $eq_id = $this->create_equipment('three');
        $this->create_mme('three');
        $response = $this->post('/mme/link_to_eq/' . $eq_id, [
            'mme_internalReference' => 'three',
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('mmes', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', $eq_id)->last()->id,
            'id' => MME::all()->where('mme_internalReference', '=', 'three')->first()->id,
        ]);

        $response = $this->post('/mme/delete/link_to_eq/' . Mme::all()->last()->id, [
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('mmes', [
            'equipmentTemp_id' => null,
            'id' => MME::all()->where('mme_internalReference', '=', 'three')->first()->id,
        ]);
    }

    /**
     * Test Conception Number: 66
     * Unlinked a mme from signed equipment
     * Expected Result: The mme is correctly unlinked from the equipment and the equipment is no longer signed
     * @returns void
     */
    public function test_add_unlink_mme_to_equipment_signed()
    {
        $eq_id = $this->create_equipment('three', 'validated');
        $this->create_mme('three', 'validated');
        $response = $this->post('/mme/link_to_eq/' . $eq_id, [
            'mme_internalReference' => 'three',
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('mmes', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', $eq_id)->last()->id,
            'id' => MME::all()->where('mme_internalReference', '=', 'three')->first()->id,
        ]);

        $response = $this->post('/equipment/validation/' . $eq_id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . $eq_id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_lifeSheetCreated' => '1',
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id,
            'eqTemp_version' => '1',
        ]);

        $response = $this->post('/mme/delete/link_to_eq/' . Mme::all()->last()->id, [
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_lifeSheetCreated' => '0',
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
            'eqTemp_version' => '2',
        ]);
        $this->assertDatabaseHas('mmes', [
            'equipmentTemp_id' => null,
            'id' => MME::all()->where('mme_internalReference', '=', 'three')->first()->id,
        ]);
    }

    /**
     * Test Conception Number: 68
     * Send the list of mme not linked to equipment
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_get_list_mme_not_linked_to_equipment()
    {
        $eq_id = $this->create_equipment('three');
        $this->create_mme('other');

        $response = $this->post('/mme/link_to_eq/' . $eq_id, [
            'mme_internalReference' => 'other',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mmes', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', $eq_id)->last()->id,
            'id' => MME::all()->where('mme_internalReference', '=', 'other')->first()->id,
        ]);

        $this->create_mme('three');
        $response = $this->get('/mme/mmes_not_linked');
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'internalReference' => 'three',
                'externalReference' => 'three',
                'name' => 'three',
                'id' => MME::all()->where('mme_internalReference', '=', 'three')->first()->id,
            ],
        ]);
    }

    /**
     * Test Conception Number: 69
     * Send the list of mme linked to equipment
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_get_list_mme_linked_to_equipment()
    {
        $eq_id = $this->create_equipment('three');
        $this->create_mme('three');
        $response = $this->post('/mme/link_to_eq/' . $eq_id, [
            'mme_internalReference' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mmes', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', $eq_id)->last()->id,
            'id' => MME::all()->where('mme_internalReference', '=', 'three')->first()->id,
        ]);
        $this->create_mme('other');
        $response = $this->get('/mme/eq_linked/' . Mme::all()->where('mme_internalReference', '=', 'three')->first()->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'eq_internalReference' => 'three',
                'eq_id' => $eq_id,
            ],
        ]);
    }

    /**
     * Test Conception Number: 69
     * Send the list of mme linked to equipment (no mme)
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_get_list_mme_linked_to_equipment_no_mme()
    {
        $eq_id = $this->create_equipment('three');
        $this->create_mme('three');
        $response = $this->get('/mme/eq_linked/' . Mme::all()->where('mme_internalReference', '=', 'three')->first()->id);
        $response->assertStatus(200);
        $response->assertJson([]);
    }

    /**
     * Test Conception Number: 70
     * Send the list of mme linked to equipment (by equipment id)
     * Expected Result: The data are correctly sent
     * @returns void
     */
    public function test_get_list_mme_linked_to_equipment_by_eq_id()
    {
        $eq_id = $this->create_equipment('three');
        $this->create_mme('three');
        $response = $this->post('/mme/link_to_eq/' . $eq_id, [
            'mme_internalReference' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mmes', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', $eq_id)->last()->id,
            'id' => MME::all()->where('mme_internalReference', '=', 'three')->first()->id,
        ]);
        $this->create_mme('other');
        $response = $this->get('/mme/send/' . $eq_id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'id' => Mme::all()->where('mme_internalReference', '=', 'three')->last()->id,
                'mme_internalReference' => 'three',
                'mme_externalReference' => 'three',
                'mme_name' => 'three',
                'mme_serialNumber' => 'three',
                'mme_constructor' => 'three',
                'mme_remarks' => 'three',
                'mme_set' => 'three',
                'mme_validate' => 'drafted',
                'mme_location' => 'three',
                'mme_signatureDate' => null,
            ],
        ]);
    }
}
