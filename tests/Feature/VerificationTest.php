<?php

namespace Tests\Feature;

use App\Models\SW01\EnumVerifAcceptanceAuthority;
use App\Models\SW01\EnumVerificationRequiredSkill;
use App\Models\SW01\Mme;
use App\Models\SW01\Verification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Conception Number: 1
     * Add mme verification as drafted with no values
     * Name: /
     * Description: /
     * Expected result: /
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a name for your verification"
     *                                          "You must enter a description for verification"
     * @returns void
     */
    public function test_add_mme_verification_as_draft_with_no_values()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'drafted',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);

        $response->assertInvalid([
            'verif_name' => 'You must enter a name for your verification',
            'verif_description' => 'You must enter a description for verification',
        ]);
    }

    public function create_mme($name, $validated = 'drafted')
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/verification/enum/requiredSkill/add', [
            'value' => 'Skill',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/verification/enum/verifAcceptanceAuthority/add', [
            'value' => 'Authority',
        ]);
        $response->assertStatus(200);

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
            'createdBy_id' => $user_id,
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

    public function create_user($name)
    {
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
     * Test Conception Number: 2
     * Add mme verification as drafted with too short name
     * Name: "in"
     * Description: /
     * Expected result: /
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter at least three characters"
     *                                          "You must enter a description for verification"
     * @returns void
     */
    public function test_add_mme_verification_as_draft_with_too_short_name()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'drafted',
            'verif_name' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);

        $response->assertInvalid([
            'verif_name' => 'You must enter at least three characters',
            'verif_description' => 'You must enter a description for verification',
        ]);
    }

    /**
     * Test Conception Number: 3
     * Add mme verification as drafted with too long name
     * Name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Description: /
     * Expected result: /
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a maximum of 100 characters"
     *                                          "You must enter a description for verification"
     * @returns void
     */
    public function test_add_mme_verification_as_draft_with_too_long_name()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'drafted',
            'verif_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_name' => 'You must enter a maximum of 100 characters',
            'verif_description' => 'You must enter a description for verification',
        ]);
    }

    /**
     * Test Conception Number: 4
     * Add mme verification as drafted with too short description
     * Name: "test"
     * Description: "in"
     * Expected result: /
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                         "You must enter at least three characters"
     * @returns void
     */
    public function test_add_mme_verification_as_draft_with_too_short_description()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'drafted',
            'verif_name' => 'test',
            'verif_description' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_description' => 'You must enter at least three characters',
        ]);
    }

    /**
     * Test Conception Number: 5
     * Add mme verification as drafted with too long description
     * Name: "test"
     * Description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected result: /
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                         "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_mme_verification_as_draft_with_too_long_description()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'drafted',
            'verif_name' => 'test',
            'verif_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_description' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 6
     * Add mme verification as drafted with too long periodicity
     * Name: "test"
     * Description: "test"
     * Expected result: "in"
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: "Annually"
     * Expected Result: Receiving an error:
     *                                         "You must enter a maximum of 4 characters"
     * @returns void
     */
    public function test_add_mme_verification_as_draft_with_too_long_periodicity()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'drafted',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_periodicity' => 'Annually',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_periodicity' => 'You must enter a maximum of 4 characters',
        ]);
    }

    /**
     * Test Conception Number: 7
     * Add mme verification as drafted with too long expected result
     * Name: "test"
     * Description: "test"
     * Expected result: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: 1
     * Expected Result: Receiving an error:
     *                                         "You must enter a maximum of 100 characters"
     * @returns void
     */
    public function test_add_mme_verification_as_draft_with_too_long_expected_result()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'drafted',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_periodicity' => 1,
            'verif_expectedResult' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_expectedResult' => 'You must enter a maximum of 100 characters',
        ]);
    }

    /**
     * Test Conception Number: 8
     * Add mme verification as drafted with too long non compliance limit
     * Name: "test"
     * Description: "test"
     * Expected result: "in"
     * Non compliance limit: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: 1
     * Expected Result: Receiving an error:
     *                                         "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_mme_verification_as_draft_with_too_long_non_compliance_limit()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'drafted',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_periodicity' => 1,
            'verif_expectedResult' => 'in',
            'verif_nonComplianceLimit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_nonComplianceLimit' => 'You must enter a maximum of 50 characters',
        ]);
    }

    /**
     * Test Conception Number: 9
     * Add mme verification as drafted with too long protocol
     * Name: "test"
     * Description: "test"
     * Expected result: "in"
     * Non compliance limit: "in"
     * Protocol: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: 1
     * Expected Result: Receiving an error:
     *                                         "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_mme_verification_as_draft_with_too_long_protocol()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'drafted',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_periodicity' => 1,
            'verif_expectedResult' => 'in',
            'verif_nonComplianceLimit' => 'in',
            'verif_protocol' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_protocol' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 10
     * Add mme verification as to be validated with no values
     * Name: /
     * Description: /
     * Expected result: /
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a name for your verification"
     *                                          "You must enter a description for verification"
     * @returns void
     */
    public function test_add_mme_verification_as_tbv_with_no_values()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'to_be_validated',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);

        $response->assertInvalid([
            'verif_name' => 'You must enter a name for your verification',
            'verif_description' => 'You must enter a description for verification',
        ]);
    }

    /**
     * Test Conception Number: 11
     * Add mme verification as to be validated with too short name
     * Name: "in"
     * Description: /
     * Expected result: /
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter at least three characters"
     *                                          "You must enter a description for verification"
     * @returns void
     */
    public function test_add_mme_verification_as_tbv_with_too_short_name()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'to_be_validated',
            'verif_name' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);

        $response->assertInvalid([
            'verif_name' => 'You must enter at least three characters',
            'verif_description' => 'You must enter a description for verification',
        ]);
    }

    /**
     * Test Conception Number: 12
     * Add mme verification as to be validated with too long name
     * Name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Description: /
     * Expected result: /
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a maximum of 100 characters"
     *                                          "You must enter a description for verification"
     * @returns void
     */
    public function test_add_mme_verification_as_tbv_with_too_long_name()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'to_be_validated',
            'verif_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_name' => 'You must enter a maximum of 100 characters',
            'verif_description' => 'You must enter a description for verification',
        ]);
    }

    /**
     * Test Conception Number: 13
     * Add mme verification as to be validated with too short description
     * Name: "test"
     * Description: "in"
     * Expected result: /
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                         "You must enter at least three characters"
     * @returns void
     */
    public function test_add_mme_verification_as_tbv_with_too_short_description()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'to_be_validated',
            'verif_name' => 'test',
            'verif_description' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_description' => 'You must enter at least three characters',
        ]);
    }

    /**
     * Test Conception Number: 14
     * Add mme verification as to be validated with too long description
     * Name: "test"
     * Description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected result: /
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                         "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_mme_verification_as_tbv_with_too_long_description()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'to_be_validated',
            'verif_name' => 'test',
            'verif_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_description' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 15
     * Add mme verification as to be validated with too long periodicity
     * Name: "test"
     * Description: "test"
     * Expected result: "in"
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: "Annually"
     * Expected Result: Receiving an error:
     *                                         "You must enter a maximum of 4 characters"
     * @returns void
     */
    public function test_add_mme_verification_as_tbv_with_too_long_periodicity()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'to_be_validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_periodicity' => 'Annually',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_periodicity' => 'You must enter a maximum of 4 characters',
        ]);
    }

    /**
     * Test Conception Number: 16
     * Add mme verification as to be validated with too long expected result
     * Name: "test"
     * Description: "test"
     * Expected result: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: 1
     * Expected Result: Receiving an error:
     *                                         "You must enter a maximum of 100 characters"
     * @returns void
     */
    public function test_add_mme_verification_as_tbv_with_too_long_expected_result()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'to_be_validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_periodicity' => 1,
            'verif_expectedResult' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_expectedResult' => 'You must enter a maximum of 100 characters',
        ]);
    }

    /**
     * Test Conception Number: 17
     * Add mme verification as to be validated with too long non compliance limit
     * Name: "test"
     * Description: "test"
     * Expected result: "in"
     * Non compliance limit: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Protocol: /
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: 1
     * Expected Result: Receiving an error:
     *                                         "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_mme_verification_as_tbv_with_too_long_non_compliance_limit()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'to_be_validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_periodicity' => 1,
            'verif_expectedResult' => 'in',
            'verif_nonComplianceLimit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_nonComplianceLimit' => 'You must enter a maximum of 50 characters',
        ]);
    }

    /**
     * Test Conception Number: 18
     * Add mme verification as to be validated with too long protocol
     * Name: "test"
     * Description: "test"
     * Expected result: "in"
     * Non compliance limit: "in"
     * Protocol: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Putting into service: /
     * Preventive Operation
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: 1
     * Expected Result: Receiving an error:
     *                                         "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_mme_verification_as_tbv_with_too_long_protocol()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'to_be_validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_periodicity' => 1,
            'verif_expectedResult' => 'in',
            'verif_nonComplianceLimit' => 'in',
            'verif_protocol' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_protocol' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 19
     * Add mme verification as validated with no values
     * Name: /
     * Description: /
     * Expected result: /
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive operation: /
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a name for your verification"
     *                                          "You must enter a description for verification"
     *                                          "You must enter an expectedResult for your verification"
     *                                          "You must enter a nonComplianceLimit for your verification"
     *                                          "You must enter a protocol for your verification"
     *                                          "You must enter a mesureUncert for your verification"
     *                                          "You must enter a mesureRange for your verification"
     *                                          "You must enter if the verification is realized during the comissioning"
     *                                          "You must enter if the verification is realized regularly?"
     *
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_no_values()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);

        $response->assertInvalid([
            'verif_name' => 'You must enter a name for your verification',
            'verif_description' => 'You must enter a description for verification',
            'verif_expectedResult' => 'You must enter an expectedResult for your verification',
            'verif_nonComplianceLimit' => 'You must enter a nonComplianceLimit for your verification',
            'verif_protocol' => 'You must enter a protocol for your verification',
            'verif_mesureUncert' => 'You must enter a mesureUncert for your verification',
            'verif_mesureRange' => 'You must enter a mesureRange for your verification',
            'verif_puttingIntoService' => 'You must enter if the verification is realized during the comissioning',
            'verif_preventiveOperation' => 'You must enter if the verification is realized regularly?',
        ]);
    }

    /**
     * Test Conception Number: 20
     * Add mme verification as validated with too short name
     * Name: "in"
     * Description: /
     * Expected result: /
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive operation: /
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter at least three characters"
     *                                          "You must enter a description for verification"
     *                                          "You must enter an expectedResult for your verification"
     *                                          "You must enter a nonComplianceLimit for your verification"
     *                                          "You must enter a protocol for your verification"
     *                                          "You must enter a mesureUncert for your verification"
     *                                          "You must enter a mesureRange for your verification"
     *                                          "You must enter if the verification is realized during the comissioning"
     *                                          "You must enter if the verification is realized regularly?"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_too_short_name()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);

        $response->assertInvalid([
            'verif_name' => 'You must enter at least three characters',
            'verif_description' => 'You must enter a description for verification',
            'verif_expectedResult' => 'You must enter an expectedResult for your verification',
            'verif_nonComplianceLimit' => 'You must enter a nonComplianceLimit for your verification',
            'verif_protocol' => 'You must enter a protocol for your verification',
            'verif_mesureUncert' => 'You must enter a mesureUncert for your verification',
            'verif_mesureRange' => 'You must enter a mesureRange for your verification',
            'verif_puttingIntoService' => 'You must enter if the verification is realized during the comissioning',
            'verif_preventiveOperation' => 'You must enter if the verification is realized regularly?',
        ]);
    }

    /**
     * Test Conception Number: 21
     * Add mme verification as validated with too long name
     * Name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Description: /
     * Expected result: /
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive operation: /
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a maximum of 100 characters"
     *                                          "You must enter a description for verification"
     *                                          "You must enter an expectedResult for your verification"
     *                                          "You must enter a nonComplianceLimit for your verification"
     *                                          "You must enter a protocol for your verification"
     *                                          "You must enter a mesureUncert for your verification"
     *                                          "You must enter a mesureRange for your verification"
     *                                          "You must enter if the verification is realized during the comissioning"
     *                                          "You must enter if the verification is realized regularly?"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_too_long_name()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_name' => 'You must enter a maximum of 100 characters',
            'verif_description' => 'You must enter a description for verification',
            'verif_expectedResult' => 'You must enter an expectedResult for your verification',
            'verif_nonComplianceLimit' => 'You must enter a nonComplianceLimit for your verification',
            'verif_protocol' => 'You must enter a protocol for your verification',
            'verif_mesureUncert' => 'You must enter a mesureUncert for your verification',
            'verif_mesureRange' => 'You must enter a mesureRange for your verification',
            'verif_puttingIntoService' => 'You must enter if the verification is realized during the comissioning',
            'verif_preventiveOperation' => 'You must enter if the verification is realized regularly?',
        ]);
    }

    /**
     * Test Conception Number: 22
     * Add mme verification as validated with too short description
     * Name: "test"
     * Description: "in"
     * Expected result: /
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive operation: /
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter at least three characters"
     *                                          "You must enter an expectedResult for your verification"
     *                                          "You must enter a nonComplianceLimit for your verification"
     *                                          "You must enter a protocol for your verification"
     *                                          "You must enter a mesureUncert for your verification"
     *                                          "You must enter a mesureRange for your verification"
     *                                          "You must enter if the verification is realized during the comissioning"
     *                                          "You must enter if the verification is realized regularly?"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_too_short_description()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_description' => 'You must enter at least three characters',
            'verif_expectedResult' => 'You must enter an expectedResult for your verification',
            'verif_nonComplianceLimit' => 'You must enter a nonComplianceLimit for your verification',
            'verif_protocol' => 'You must enter a protocol for your verification',
            'verif_mesureUncert' => 'You must enter a mesureUncert for your verification',
            'verif_mesureRange' => 'You must enter a mesureRange for your verification',
            'verif_puttingIntoService' => 'You must enter if the verification is realized during the comissioning',
            'verif_preventiveOperation' => 'You must enter if the verification is realized regularly?',
        ]);
    }

    /**
     * Test Conception Number: 23
     * Add mme verification as validated with too long description
     * Name: "test"
     * Description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Expected result: /
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive operation: /
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a maximum of 255 characters"
     *                                          "You must enter an expectedResult for your verification"
     *                                          "You must enter a nonComplianceLimit for your verification"
     *                                          "You must enter a protocol for your verification"
     *                                          "You must enter a mesureUncert for your verification"
     *                                          "You must enter a mesureRange for your verification"
     *                                          "You must enter if the verification is realized during the comissioning"
     *                                          "You must enter if the verification is realized regularly?"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_too_long_description()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_description' => 'You must enter a maximum of 255 characters',
            'verif_expectedResult' => 'You must enter an expectedResult for your verification',
            'verif_nonComplianceLimit' => 'You must enter a nonComplianceLimit for your verification',
            'verif_protocol' => 'You must enter a protocol for your verification',
            'verif_mesureUncert' => 'You must enter a mesureUncert for your verification',
            'verif_mesureRange' => 'You must enter a mesureRange for your verification',
            'verif_puttingIntoService' => 'You must enter if the verification is realized during the comissioning',
            'verif_preventiveOperation' => 'You must enter if the verification is realized regularly?',
        ]);
    }

    /**
     * Test Conception Number: 24
     * Add mme verification as validated with too short expectedResult
     * Name: "test"
     * Description: "test"
     * Expected result: "in"
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive operation: /
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter at least three character"
     *                                          "You must enter a nonComplianceLimit for your verification"
     *                                          "You must enter a protocol for your verification"
     *                                          "You must enter a mesureUncert for your verification"
     *                                          "You must enter a mesureRange for your verification"
     *                                          "You must enter if the verification is realized during the comissioning"
     *                                          "You must enter if the verification is realized regularly?"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_too_short_expectedResult()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_expectedResult' => 'You must enter at least three character',
            'verif_nonComplianceLimit' => 'You must enter a nonComplianceLimit for your verification',
            'verif_protocol' => 'You must enter a protocol for your verification',
            'verif_mesureUncert' => 'You must enter a mesureUncert for your verification',
            'verif_mesureRange' => 'You must enter a mesureRange for your verification',
            'verif_puttingIntoService' => 'You must enter if the verification is realized during the comissioning',
            'verif_preventiveOperation' => 'You must enter if the verification is realized regularly?',
        ]);
    }

    /**
     * Test Conception Number: 25
     * Add mme verification as validated with too long expectedResult
     * Name: "test"
     * Description: "test"
     * Expected result: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Non compliance limit: /
     * Protocol: /
     * Putting into service: /
     * Preventive operation: /
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a maximum of 100 characters"
     *                                          "You must enter a nonComplianceLimit for your verification"
     *                                          "You must enter a protocol for your verification"
     *                                          "You must enter a mesureUncert for your verification"
     *                                          "You must enter a mesureRange for your verification"
     *                                          "You must enter if the verification is realized during the comissioning"
     *                                          "You must enter if the verification is realized regularly?"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_too_long_expectedResult()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_expectedResult' => 'You must enter a maximum of 100 characters',
            'verif_nonComplianceLimit' => 'You must enter a nonComplianceLimit for your verification',
            'verif_protocol' => 'You must enter a protocol for your verification',
            'verif_mesureUncert' => 'You must enter a mesureUncert for your verification',
            'verif_mesureRange' => 'You must enter a mesureRange for your verification',
            'verif_puttingIntoService' => 'You must enter if the verification is realized during the comissioning',
            'verif_preventiveOperation' => 'You must enter if the verification is realized regularly?',
        ]);
    }

    /**
     * Test Conception Number: 26
     * Add mme verification as validated with too short nonComplianceLimit
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "in"
     * Protocol: /
     * Putting into service: /
     * Preventive operation: /
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter at least three character"
     *                                          "You must enter a protocol for your verification"
     *                                          "You must enter a mesureUncert for your verification"
     *                                          "You must enter a mesureRange for your verification"
     *                                          "You must enter if the verification is realized during the comissioning"
     *                                          "You must enter if the verification is realized regularly?"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_too_short_nonComplianceLimit()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_nonComplianceLimit' => 'You must enter at least three character',
            'verif_protocol' => 'You must enter a protocol for your verification',
            'verif_mesureUncert' => 'You must enter a mesureUncert for your verification',
            'verif_mesureRange' => 'You must enter a mesureRange for your verification',
            'verif_puttingIntoService' => 'You must enter if the verification is realized during the comissioning',
            'verif_preventiveOperation' => 'You must enter if the verification is realized regularly?',
        ]);
    }

    /**
     * Test Conception Number: 27
     * Add mme verification as validated with too long nonComplianceLimit
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Protocol: /
     * Putting into service: /
     * Preventive operation: /
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a maximum of 50 characters"
     *                                          "You must enter a protocol for your verification"
     *                                          "You must enter a mesureUncert for your verification"
     *                                          "You must enter a mesureRange for your verification"
     *                                          "You must enter if the verification is realized during the comissioning"
     *                                          "You must enter if the verification is realized regularly?"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_too_long_nonComplianceLimit()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_nonComplianceLimit' => 'You must enter a maximum of 50 characters',
            'verif_protocol' => 'You must enter a protocol for your verification',
            'verif_mesureUncert' => 'You must enter a mesureUncert for your verification',
            'verif_mesureRange' => 'You must enter a mesureRange for your verification',
            'verif_puttingIntoService' => 'You must enter if the verification is realized during the comissioning',
            'verif_preventiveOperation' => 'You must enter if the verification is realized regularly?',
        ]);
    }

    /**
     * Test Conception Number: 28
     * Add mme verification as validated with too short protocol
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "in"
     * Putting into service: /
     * Preventive operation: /
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter at least three characters"
     *                                          "You must enter a mesureUncert for your verification"
     *                                          "You must enter a mesureRange for your verification"
     *                                          "You must enter if the verification is realized during the comissioning"
     *                                          "You must enter if the verification is realized regularly?"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_too_short_protocol()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'in',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_protocol' => 'You must enter at least three characters',
            'verif_mesureUncert' => 'You must enter a mesureUncert for your verification',
            'verif_mesureRange' => 'You must enter a mesureRange for your verification',
            'verif_puttingIntoService' => 'You must enter if the verification is realized during the comissioning',
            'verif_preventiveOperation' => 'You must enter if the verification is realized regularly?',
        ]);
    }

    /**
     * Test Conception Number: 29
     * Add mme verification as validated with too long protocol
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Putting into service: /
     * Preventive operation: /
     * Measurement uncertainty: /
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a maximum of 255 characters"
     *                                          "You must enter a mesureUncert for your verification"
     *                                          "You must enter a mesureRange for your verification"
     *                                          "You must enter if the verification is realized during the comissioning"
     *                                          "You must enter if the verification is realized regularly?"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_too_long_protocol()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_protocol' => 'You must enter a maximum of 255 characters',
            'verif_mesureUncert' => 'You must enter a mesureUncert for your verification',
            'verif_mesureRange' => 'You must enter a mesureRange for your verification',
            'verif_puttingIntoService' => 'You must enter if the verification is realized during the comissioning',
            'verif_preventiveOperation' => 'You must enter if the verification is realized regularly?',
        ]);
    }

    /**
     * Test Conception Number: 30
     * Add mme verification as validated with mesureUncert
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "test"
     * Putting into service: /
     * Preventive operation: /
     * Measurement uncertainty: "test"
     * Measurement range: /
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter a mesureRange for your verification"
     *                                          "You must enter if the verification is realized during the comissioning"
     *                                          "You must enter if the verification is realized regularly?"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_mesureUncert()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_mesureRange' => 'You must enter a mesureRange for your verification',
            'verif_puttingIntoService' => 'You must enter if the verification is realized during the comissioning',
            'verif_preventiveOperation' => 'You must enter if the verification is realized regularly?',
        ]);
    }

    /**
     * Test Conception Number: 31
     * Add mme verification as validated with mesureRange
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "test"
     * Putting into service: /
     * Preventive operation: /
     * Measurement uncertainty: "test"
     * Measurement range: "test"
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter if the verification is realized during the comissioning"
     *                                          "You must enter if the verification is realized regularly?"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_mesureRange()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_puttingIntoService' => 'You must enter if the verification is realized during the comissioning',
            'verif_preventiveOperation' => 'You must enter if the verification is realized regularly?',
        ]);
    }

    /**
     * Test Conception Number: 32
     * Add mme verification as validated with puttingIntoService
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "test"
     * Putting into service: false
     * Preventive operation: /
     * Measurement uncertainty: "test"
     * Measurement range: "test"
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                          "You must enter if the verification is realized regularly?"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_puttingIntoService()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_preventiveOperation' => 'You must enter if the verification is realized regularly?',
        ]);
    }

    /**
     * Test Conception Number: 33
     * Add mme verification as validated with preventiveOperation
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "test"
     * Putting into service: false
     * Preventive operation: false
     * Measurement uncertainty: "test"
     * Measurement range: "test"
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must choose a required skill"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_preventiveOperation()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => false,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_requiredSkill' => 'You must choose a required skill',
        ]);
    }

    /**
     * Test Conception Number: 34
     * Add mme verification as validated with requiredSkill
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "test"
     * Putting into service: false
     * Preventive operation: false
     * Measurement uncertainty: "test"
     * Measurement range: "test"
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must choose a verif acceptance authority"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_requiredSkill()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => false,
            'verif_requiredSkill' => 'Skill',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_verifAcceptanceAuthority' => 'You must choose a verif acceptance authority',
        ]);
    }

    /**
     * Test Conception Number: 35
     * Add mme verification as validated with verifAcceptanceAuthority
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "test"
     * Putting into service: false
     * Preventive operation: false
     * Measurement uncertainty: "test"
     * Measurement range: "test"
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must choose a verif acceptance authority"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_verifAcceptanceAuthority()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => false,
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => false,
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => false,
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);
    }

    /**
     * Test Conception Number: 36
     * Add mme verification as validated with preventive operation at true
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "test"
     * Putting into service: false
     * Preventive operation: false
     * Measurement uncertainty: "test"
     * Measurement range: "test"
     * Periodicity: /
     * Expected Result: Receiving an error:
     *                                      "You must enter a periodicity for your verification"
     *                                      "You must enter a periodicity symbol for your verification"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_preventiveOperation_true()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_periodicity' => 'You must enter a periodicity for your verification',
            'verif_symbolPeriodicity' => 'You must enter a periodicity symbol for your verification',
        ]);
    }

    /**
     * Test Conception Number: 37
     * Add mme verification as validated with preventive operation at true and too long periodicity
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "test"
     * Putting into service: false
     * Preventive operation: false
     * Measurement uncertainty: "test"
     * Measurement range: "test"
     * Periodicity: "Annually"
     * Expected Result: Receiving an error:
     *                                      "You must enter a maximum of 4 characters"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_preventiveOperation_true_and_too_long_periodicity()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 'Annually',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'verif_periodicity' => 'You must enter a maximum of 4 characters',
        ]);
    }

    /**
     * Test Conception Number: 38
     * Add mme verification as validated with preventive operation at true and correct values
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "test"
     * Putting into service: false
     * Preventive operation: false
     * Measurement uncertainty: "test"
     * Measurement range: "test"
     * Periodicity: 1
     * Periodicity symbol: "Y"
     * Expected Result: Receiving an error:
     *                                      "You must choose a required skill"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_preventiveOperation_true_and_correct_values()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_requiredSkill' => 'You must choose a required skill',
        ]);
    }

    /**
     * Test Conception Number: 39
     * Add mme verification as validated with correct values and required skill
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "test"
     * Putting into service: false
     * Preventive operation: false
     * Measurement uncertainty: "test"
     * Measurement range: "test"
     * Periodicity: 1
     * Periodicity symbol: "Y"
     * Required skill: "test"
     * Expected Result: Receiving an error:
     *                                      "You must choose a verif acceptance authority"
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_correct_values_and_required_skill()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_verifAcceptanceAuthority' => 'You must choose a verif acceptance authority',
        ]);
    }

    /**
     * Test Conception Number: 40
     * Add mme verification as validated with correct values, required skill and verif acceptance authority
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "test"
     * Putting into service: false
     * Preventive operation: false
     * Measurement uncertainty: "test"
     * Measurement range: "test"
     * Periodicity: 1
     * Periodicity symbol: "Y"
     * Required skill: "test"
     * Verif acceptance authority: "test"
     * Expected Result: The verification is added to the database
     * @returns void
     */
    public function test_add_mme_verification_as_validated_with_correct_values_required_skill_and_verif_acceptance_authority()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
            'verif_nextDate' => Carbon::create(Verification::all()->last()->verif_startDate)->addYear(),
        ]);

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'M',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'M',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'M',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
            'verif_nextDate' => Carbon::create(Verification::all()->last()->verif_startDate)->addMonth(),
        ]);

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'D',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'D',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'D',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
            'verif_nextDate' => Carbon::create(Verification::all()->last()->verif_startDate)->addDay(),
        ]);

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'H',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'H',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'H',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
            'verif_nextDate' => Carbon::create(Verification::all()->last()->verif_startDate)->addHour(),
        ]);
    }

    /**
     * Test Conception Number: 41
     * Add mme verification with too high values of periodicity
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "test"
     * Putting into service: false
     * Preventive operation: false
     * Measurement uncertainty: "test"
     * Measurement range: "test"
     * Periodicity: 1
     * Periodicity symbol: "Y"
     * Required skill: "test"
     * Verif acceptance authority: "test"
     * Expected Result: Receiving an error:
     *                                      - First case : "You can't enter a periodicity higher than 15 years"
     *                                      - Second case : "You can't enter a periodicity higher than 180 months"
     *                                      - Third case : "You can't enter a periodicity higher than 5475 days"
     * @returns void
     */
    public function test_add_mme_verification_with_too_high_values_of_periodicity()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 20,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_periodicity' => 'You can\'t enter a periodicity higher than 15 years',
        ]);

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 200,
            'verif_symbolPeriodicity' => 'M',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_periodicity' => 'You can\'t enter a periodicity higher than 180 months',
        ]);

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 6000,
            'verif_symbolPeriodicity' => 'D',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_periodicity' => 'You can\'t enter a periodicity higher than 5475 days',
        ]);
    }

    /**
     * Test Conception Number: 42
     * Add mme verification to a signed mme
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "test"
     * Putting into service: false
     * Preventive operation: false
     * Measurement uncertainty: "test"
     * Measurement range: "test"
     * Periodicity: 0
     * Periodicity symbol: "Y"
     * Required skill: "test"
     * Verif acceptance authority: "test"
     * Expected Result: The verification is added to the mme and the mme is no longer signed
     * @returns void
     */
    public function test_add_mme_verification_to_a_signed_mme()
    {
        $mme_id = $this->create_mme('test', 'validated');

        $response = $this->post('/mme/validation/' . $mme_id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . $mme_id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'H',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'H',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'H',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->last()->id,
            'mmeTemp_version' => 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
        ]);
    }

    /**
     * Test Conception Number: 43
     * Update mme verification
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "test"
     * Putting into service: false
     * Preventive operation: false
     * Measurement uncertainty: "test"
     * Measurement range: "test"
     * Periodicity: 0
     * Periodicity symbol: "Y"
     * Required skill: "test"
     * Verif acceptance authority: "test"
     * Expected Result: The verification is updated in the database
     * @returns void
     */
    public function test_update_mme_verification()
    {
        $mme_id = $this->create_mme('test');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        $response = $this->post('/mme/update/verif/' . Verification::all()->last()->id, [
            'verif_validate' => 'validated',
            'verif_name' => 'test2',
            'verif_description' => 'test2',
            'verif_expectedResult' => 'test2',
            'verif_nonComplianceLimit' => 'test2',
            'verif_protocol' => 'test2',
            'verif_mesureUncert' => 'test2',
            'verif_mesureRange' => 'test2',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'mme_id' => $mme_id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test2',
            'verif_description' => 'test2',
            'verif_expectedResult' => 'test2',
            'verif_nonComplianceLimit' => 'test2',
            'verif_protocol' => 'test2',
            'verif_mesureUncert' => 'test2',
            'verif_mesureRange' => 'test2',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        $response = $this->post('/mme/update/verif/' . Verification::all()->last()->id, [
            'verif_validate' => 'validated',
            'verif_name' => 'test2',
            'verif_description' => 'test2',
            'verif_expectedResult' => 'test2',
            'verif_nonComplianceLimit' => 'test2',
            'verif_protocol' => 'test2',
            'verif_mesureUncert' => 'test2',
            'verif_mesureRange' => 'test2',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'M',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'mme_id' => $mme_id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test2',
            'verif_description' => 'test2',
            'verif_expectedResult' => 'test2',
            'verif_nonComplianceLimit' => 'test2',
            'verif_protocol' => 'test2',
            'verif_mesureUncert' => 'test2',
            'verif_mesureRange' => 'test2',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'M',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        $response = $this->post('/mme/update/verif/' . Verification::all()->last()->id, [
            'verif_validate' => 'validated',
            'verif_name' => 'test2',
            'verif_description' => 'test2',
            'verif_expectedResult' => 'test2',
            'verif_nonComplianceLimit' => 'test2',
            'verif_protocol' => 'test2',
            'verif_mesureUncert' => 'test2',
            'verif_mesureRange' => 'test2',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'D',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'mme_id' => $mme_id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test2',
            'verif_description' => 'test2',
            'verif_expectedResult' => 'test2',
            'verif_nonComplianceLimit' => 'test2',
            'verif_protocol' => 'test2',
            'verif_mesureUncert' => 'test2',
            'verif_mesureRange' => 'test2',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'D',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        $response = $this->post('/mme/update/verif/' . Verification::all()->last()->id, [
            'verif_validate' => 'validated',
            'verif_name' => 'test2',
            'verif_description' => 'test2',
            'verif_expectedResult' => 'test2',
            'verif_nonComplianceLimit' => 'test2',
            'verif_protocol' => 'test2',
            'verif_mesureUncert' => 'test2',
            'verif_mesureRange' => 'test2',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'H',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'mme_id' => $mme_id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test2',
            'verif_description' => 'test2',
            'verif_expectedResult' => 'test2',
            'verif_nonComplianceLimit' => 'test2',
            'verif_protocol' => 'test2',
            'verif_mesureUncert' => 'test2',
            'verif_mesureRange' => 'test2',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'H',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);
    }

    /**
     * Test Conception Number: 43
     * Update mme verification linked to signed mme
     * Name: "test"
     * Description: "test"
     * Expected result: "test"
     * Non compliance limit: "test"
     * Protocol: "test"
     * Putting into service: false
     * Preventive operation: false
     * Measurement uncertainty: "test"
     * Measurement range: "test"
     * Periodicity: 0
     * Periodicity symbol: "Y"
     * Required skill: "test"
     * Verif acceptance authority: "test"
     * Expected Result: The verification is updated in the database and the mme is no longer signed
     * @returns void
     */
    public function test_update_mme_verification_signed()
    {
        $mme_id = $this->create_mme('test', 'validated');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        $response = $this->post('/mme/validation/' . $mme_id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . $mme_id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/update/verif/' . Verification::all()->last()->id, [
            'verif_validate' => 'validated',
            'verif_name' => 'test2',
            'verif_description' => 'test2',
            'verif_expectedResult' => 'test2',
            'verif_nonComplianceLimit' => 'test2',
            'verif_protocol' => 'test2',
            'verif_mesureUncert' => 'test2',
            'verif_mesureRange' => 'test2',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'mme_id' => $mme_id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test2',
            'verif_description' => 'test2',
            'verif_expectedResult' => 'test2',
            'verif_nonComplianceLimit' => 'test2',
            'verif_protocol' => 'test2',
            'verif_mesureUncert' => 'test2',
            'verif_mesureRange' => 'test2',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->last()->id,
            'mmeTemp_version' => 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
        ]);
    }

    /**
     * Test Conception Number: 44
     * Send mme verification list (id of mme)
     * Expected Result: The data are sent correctly
     * @returns void
     */
    public function test_send_mme_verification_list()
    {
        $mme_id = $this->create_mme('test', 'validated');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        $response = $this->get('/verifs/send/' . $mme_id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'id' => Verification::all()->last()->id,
                'verif_number' => '1',
                'verif_description' => 'test',
                'verif_periodicity' => '1',
                'verif_symbolPeriodicity' => 'Y',
                'verif_protocol' => 'test',
                'verif_reformDate' => null,
                'verif_name' => 'test',
                'verif_expectedResult' => 'test',
                'verif_nonComplianceLimit' => 'test',
                'verif_requiredSkill' => 'Skill',
                'verif_verifAcceptanceAuthority' => 'Authority',
                'verif_validate' => 'validated',
                'verif_puttingIntoService' => false,
                'verif_preventiveOperation' => true,
                'verif_mesureUncert' => 'test',
                'verif_mesureRange' => 'test'
            ]
        ]);
    }

    /**
     * Test Conception Number: 45
     * Send mme verification list at lifesheet format (id of mme)
     * Expected Result: The data are sent correctly
     * @returns void
     */
    public function test_send_mme_verification_list_lifesheet()
    {
        $mme_id = $this->create_mme('test', 'validated');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        $response = $this->get('/verifs/send/lifesheet/' . $mme_id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'id' => Verification::all()->last()->id,
                'verif_number' => '1',
                'verif_description' => 'test',
                'verif_periodicity' => '1',
                'verif_symbolPeriodicity' => 'Y',
                'verif_protocol' => 'test',
                'verif_reformDate' => null,
                'verif_name' => 'test',
                'verif_expectedResult' => 'test',
                'verif_nonComplianceLimit' => 'test',
                'verif_requiredSkill' => 'Skill',
                'verif_verifAcceptanceAuthority' => 'Authority',
                'verif_validate' => 'validated',
                'verif_puttingIntoService' => 'No',
                'verif_preventiveOperation' => 'Yes',
                'verif_reformed' => 'no',
                'verif_mesureUncert' => 'test',
                'verif_mesureRange' => 'test'
            ]
        ]);
    }

    /**
     * Test Conception Number: 45
     * Send mme verification list (id of verification)
     * Expected Result: The data are sent correctly
     * @returns void
     */
    public function test_send_mme_verification_list_id()
    {
        $mme_id = $this->create_mme('test', 'validated');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        $response = $this->get('/verif/send/' . Verification::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'id' => Verification::all()->last()->id,
                'verif_number' => '1',
                'verif_description' => 'test',
                'verif_periodicity' => '1',
                'verif_symbolPeriodicity' => 'Y',
                'verif_protocol' => 'test',
                'verif_reformDate' => null,
                'verif_name' => 'test',
                'verif_expectedResult' => 'test',
                'verif_nonComplianceLimit' => 'test',
                'verif_requiredSkill' => 'Skill',
                'verif_verifAcceptanceAuthority' => 'Authority',
                'verif_validate' => 'validated',
                'verif_puttingIntoService' => false,
                'verif_preventiveOperation' => true,
                'verif_mesureUncert' => 'test',
                'verif_mesureRange' => 'test'
            ]
        ]);
    }

    /**
     * Test Conception Number: 46
     * Send mme verification list linked to a validated mme (id of mme)
     * Expected Result: The data are sent correctly
     * @returns void
     */
    public function test_send_mme_verification_list_validated_mme_id()
    {
        $mme_id = $this->create_mme('test', 'validated');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        $response = $this->post('/mme/validation/' . $mme_id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . $mme_id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->get('/verif/send/validated/' . $mme_id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'id' => Verification::all()->last()->id,
                'verif_number' => '1',
                'verif_description' => 'test',
                'verif_periodicity' => '1',
                'verif_symbolPeriodicity' => 'Y',
                'verif_protocol' => 'test',
                'verif_reformDate' => null,
                'verif_name' => 'test',
                'verif_expectedResult' => 'test',
                'verif_nonComplianceLimit' => 'test',
                'verif_requiredSkill' => 'Skill',
                'verif_verifAcceptanceAuthority' => 'Authority',
                'verif_validate' => 'validated',
                'verif_puttingIntoService' => false,
                'verif_preventiveOperation' => true,
                'verif_mesureUncert' => 'test',
                'verif_mesureRange' => 'test'
            ]
        ]);
    }

    /**
     * Test Conception Number: 47
     * Delete mme verification
     * Expected Result: The data are deleted correctly
     * @returns void
     */
    public function test_delete_mme_verification()
    {
        $mme_id = $this->create_mme('test', 'validated');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        $response = $this->post('/mme/delete/verif/' . Verification::all()->last()->id, [
            'mme_id' => $mme_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test Conception Number: 48
     * Delete mme verification linked to signed mme
     * Expected Result: The data are deleted correctly
     * @returns void
     */
    public function test_delete_mme_verification_linked_to_signed_mme()
    {
        $mme_id = $this->create_mme('test', 'validated');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        $response = $this->post('/mme/validation/' . $mme_id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . $mme_id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/delete/verif/' . Verification::all()->last()->id, [
            'mme_id' => $mme_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->last()->id,
            'mmeTemp_version' => 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
        ]);
    }

    /**
     * Test Conception Number: 49
     * Reform mme verification
     * Expected Result: The verification is reformed correctly
     * @returns void
     */
    public function test_reform_mme_verification()
    {
        $mme_id = $this->create_mme('test', 'validated');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        $response = $this->post('/mme/reform/verif/' . Verification::all()->last()->id, [
            'mme_id' => $mme_id,
            'user_id' => User::all()->last()->id,
            'verif_reformDate' => Carbon::now()->addDays(5)->format('Y-m-d'),
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
            'verif_reformDate' => Carbon::now()->addDays(5)->format('Y-m-d'),
        ]);
    }

    /**
     * Test Conception Number: 50
     * Reform mme verification with a date before the start date of the verification
     * Expected Result: Receive an error:
     *                                      "You must entered a reformDate that is after the startDate"
     * @returns void
     */
    public function test_reform_mme_verification_with_a_date_before_the_start_date_of_the_verification()
    {
        $mme_id = $this->create_mme('test', 'validated');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        $response = $this->post('/mme/reform/verif/' . Verification::all()->last()->id, [
            'mme_id' => $mme_id,
            'user_id' => User::all()->last()->id,
            'verif_reformDate' => Carbon::now()->subDays(8)->format('Y-m-d')
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_reformDate' => 'You must entered a reformDate that is after the startDate'
        ]);
    }

    /**
     * Test Conception Number: 51
     * Reform mme verification with a date older than one month
     * Expected Result: Receive an error:
     *                                      "You can't enter a date that is older than one month"
     * @returns void
     */
    public function test_reform_mme_verification_with_a_date_older_than_one_month()
    {
        $mme_id = $this->create_mme('test', 'validated');

        $response = $this->post('/verif/verif', [
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->post('/mme/add/verif', [
            'mme_id' => $mme_id,
            'verif_validate' => 'validated',
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'verif_requiredSkill' => 'Skill',
            'verif_verifAcceptanceAuthority' => 'Authority',
        ]);
        $this->assertDatabaseHas('verifications', [
            'verif_name' => 'test',
            'verif_description' => 'test',
            'verif_expectedResult' => 'test',
            'verif_nonComplianceLimit' => 'test',
            'verif_protocol' => 'test',
            'verif_mesureUncert' => 'test',
            'verif_mesureRange' => 'test',
            'verif_puttingIntoService' => false,
            'verif_preventiveOperation' => true,
            'verif_periodicity' => 1,
            'verif_symbolPeriodicity' => 'Y',
            'enumRequiredSkill_id' => EnumVerificationRequiredSkill::all()->last()->id,
            'enumVerifAcceptanceAuthority_id' => EnumVerifAcceptanceAuthority::all()->last()->id,
        ]);

        Verification::all()->last()->update([
            'verif_startDate' => Carbon::now()->subYears(2)->format('Y-m-d'),
        ]);

        $response = $this->post('/mme/reform/verif/' . Verification::all()->last()->id, [
            'mme_id' => $mme_id,
            'user_id' => User::all()->last()->id,
            'verif_reformDate' => Carbon::now()->subMonths(2)->format('Y-m-d')
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'verif_reformDate' => 'You can\'t enter a date that is older than one month'
        ]);
    }
}
