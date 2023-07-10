<?php

namespace Tests\Feature;


use App\Models\SW01\Mme;
use App\Models\SW01\MmeState;
use App\Models\SW01\MmeTemp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MmeStateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Conception Number: 1
     * Try to add a new state with no values
     * Remarks: /
     * Start Date : /
     * End Date : /
     * Name : /
     * Expected Result: Receiving an error :
     *                                          "You must enter a remark about the state"
     * @returns void
     */
    public function test_add_state_no_values()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme_state/verif', [
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'state_remarks' => 'You must enter a remark about the state',
        ]);
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
     * Try to add a new state with too short remarks
     * Remarks: "in"
     * Start Date : /
     * End Date : /
     * Name : /
     * Expected Result: Receiving an error :
     *                                          "You must enter at least three characters"
     * @returns void
     */
    public function test_add_state_short_remarks()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'in',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'state_remarks' => 'You must enter at least three characters',
        ]);
    }

    /**
     * Test Conception Number: 3
     * Try to add a new state with too long remarks
     * Remarks: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Start Date : /
     * End Date : /
     * Name : /
     * Expected Result: Receiving an error :
     *                                          "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_state_long_remarks()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'state_remarks' => 'You must enter a maximum of 255 characters',
        ]);
    }

    /**
     * Test Conception Number: 4
     * Try to add a new state with only a correct remark
     * Remarks: "Remarks"
     * Start Date : /
     * End Date : /
     * Name : /
     * Expected Result: Receiving an error :
     *                                          "You must choose a name for your state"
     * @returns void
     */
    public function test_add_state_only_remarks()
    {
        $user_id = $this->create_user('test');
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You must choose a name for your state',
        ]);
    }

    /**
     * Test Conception Number: 5
     * Try to add a new state with no dates
     * Remarks: "Remarks"
     * Start Date : /
     * End Date : /
     * Name : "Name"
     * Expected Result: Receiving an error :
     *                                          "You must entered a start date for your state"
     * @returns void
     */
    public function test_add_state_no_dates()
    {
        $user_id = $this->create_user('test');
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Name',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_startDate' => 'You must entered a start date for your state',
        ]);
    }

    /**
     * Test Conception Number: 6
     * Try to add a new state with a too old start date
     * Remarks: "Remarks"
     * Start Date: Today - 2 Months
     * End Date: /
     * Name: "Name"
     * Expected Result: Receiving an error :
     *                                          "You can't enter a date that is older than one month"
     * @returns void
     */
    public function test_add_state_old_start_date()
    {
        $user_id = $this->create_user('test');

        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Name',
            'state_startDate' => Carbon::now()->subMonths(2),
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_startDate' => 'You can\'t enter a date that is older than one month',
        ]);
    }

    /**
     * Test Conception Number: 7
     * Try to add a new state with an incorrect name
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Name"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting to be in use state from this one"
     * @returns void
     */
    public function test_add_state_incorrect_name()
    {
        $eq_id = $this->create_mme('three');
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Name',
            'state_startDate' => Carbon::now(),
            'mme_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting to be in use state from this one',
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
        $countMme = Mme::all()->count();
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
        $this->assertEquals($countMme + 1, Mme::all()->count());
        return Mme::all()->where('mme_internalReference', '=', $name)->last()->id;
    }


    /**
     * Corresponding state with each number :
     * Effective for each following tests
     * 1 : 'Waiting_for_referencing',
     * 2 : 'Waiting_to_be_in_use',
     * 3 : 'In_use',
     * 4 : 'Under_verification',
     * 5 : 'In_quarantine',
     * 6 : 'Under_repair',
     * 7 : 'Broken',
     * 8 : 'Downgraded',
     * 9 : 'Reformed',
     * 10 : 'Lost'
     */

    /**
     * Test Conception Number: 8
     * Try to add a new state (Waiting_for_referencing -> Waiting_for_referencing)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_to_be_in_use"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting to be in use state from this one"
     * @returns void
     */
    public function test_add_state_1_to_1()
    {
        $eq_id = $this->create_mme('three');
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'mme_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting to be in use state from this one',
        ]);
    }

    /**
     * Test Conception Number: 8
     * Try to add a new state (Waiting_for_referencing -> Waiting_to_be_in_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_to_be_in_use"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_1_to_2()
    {
        $eq_id = $this->create_mme('three');
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => $date,
            'mme_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 9
     * Try to add a new state (Waiting_for_referencing -> In_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_use"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting to be in use state from this one"
     * @returns void
     */
    public function test_add_state_1_to_3()
    {
        $eq_id = $this->create_mme('three');
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'mme_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting to be in use state from this one',
        ]);
    }

    /**
     * Test Conception Number: 10
     * Try to add a new state (Waiting_for_referencing -> Under_verification)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_verification"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting to be in use state from this one"
     * @returns void
     */
    public function test_add_state_1_to_4()
    {
        $eq_id = $this->create_mme('three');
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date,
            'mme_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting to be in use state from this one',
        ]);
    }

    /**
     * Test Conception Number: 11
     * Try to add a new state (Waiting_for_referencing -> In_quarantine)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_quarantine"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting to be in use state from this one"
     * @returns void
     */
    public function test_add_state_1_to_5()
    {
        $eq_id = $this->create_mme('three');
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date,
            'mme_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting to be in use state from this one',
        ]);
    }

    /**
     * Test Conception Number: 12
     * Try to add a new state (Waiting_for_referencing -> Under_repair)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_repair"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting to be in use state from this one"
     * @returns void
     */
    public function test_add_state_1_to_6()
    {
        $eq_id = $this->create_mme('three');
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'mme_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting to be in use state from this one',
        ]);
    }

    /**
     * Test Conception Number: 13
     * Try to add a new state (Waiting_for_referencing -> Broken)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Broken"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting to be in use state from this one"
     * @returns void
     */
    public function test_add_state_1_to_7()
    {
        $eq_id = $this->create_mme('three');
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'mme_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting to be in use state from this one',
        ]);
    }

    /**
     * Test Conception Number: 14
     * Try to add a new state (Waiting_for_referencing -> Downgraded)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Downgraded"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting to be in use state from this one"
     * @returns void
     */
    public function test_add_state_1_to_8()
    {
        $eq_id = $this->create_mme('three');
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'mme_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting to be in use state from this one',
        ]);
    }

    /**
     * Test Conception Number: 15
     * Try to add a new state (Waiting_for_referencing -> Reformed)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Reformed"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting to be in use state from this one"
     * @returns void
     */
    public function test_add_state_1_to_9()
    {
        $eq_id = $this->create_mme('three');
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'mme_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting to be in use state from this one',
        ]);
    }

    /**
     * Test Conception Number: 16
     * Try to add a new state (Waiting_for_referencing -> Lost)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Destroyed"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting to be in use state from this one"
     * @returns void
     */
    public function test_add_state_1_to_10()
    {
        $eq_id = $this->create_mme('three');
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Destroyed',
            'state_startDate' => $date,
            'mme_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting to be in use state from this one',
        ]);
    }

    /**
     * Test Conception Number: 17
     * Try to add a new state (Waiting_to_be_in_use -> Waiting_for_referencing)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_referencing"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_2_to_1()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 18
     * Try to add a new state (Waiting_to_be_in_use -> Waiting_to_be_in_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_to_be_in_use"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting for referencing, in use, in quarantine, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_2_to_2()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, in use, in quarantine, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 19
     * Try to add a new state (Waiting_to_be_in_use -> In_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_use"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_2_to_3()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 20
     * Try to add a new state (Waiting_to_be_in_use -> Under_verification)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_verification"
     * Expected Result: Receiving an error :
     *                                         "You can't only go in waiting for referencing, in use, in quarantine, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_2_to_4()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, in use, in quarantine, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 21
     * Try to add a new state (Waiting_to_be_in_use -> In_quarantine)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_quarantine"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_2_to_5()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 22
     * Try to add a new state (Waiting_to_be_in_use -> Under_repair)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_repair"
     * Expected Result: Receiving an error :
     *                                         "You can't only go in waiting for referencing, in use, in quarantine, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_2_to_6()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, in use, in quarantine, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 23
     * Try to add a new state (Waiting_to_be_in_use -> Broken)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Broken"
     * Expected Result: Receiving an error :
     *                                         "You can't only go in waiting for referencing, in use, in quarantine, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_2_to_7()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, in use, in quarantine, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 24
     * Try to add a new state (Waiting_to_be_in_use -> Downgraded)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Downgraded"
     * Expected Result: Receiving an error :
     *                                         "You can't only go in waiting for referencing, in use, in quarantine, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_2_to_8()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, in use, in quarantine, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 25
     * Try to add a new state (Waiting_to_be_in_use -> Reformed)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Reformed"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_2_to_9()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 26
     * Try to add a new state (Waiting_to_be_in_use -> Lost)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Lost"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_2_to_10()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 27
     * Try to add a new state (In_use -> Waiting_for_referencing)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_referencing"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_3_to_1()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 28
     * Try to add a new state (In_use -> Waiting_to_be_in_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_to_be_in_use"
     * Expected Result: Receiving an error :
     *                                         "You can't only go in waiting for referencing, Under_verification, In_quarantine, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_3_to_2()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, Under_verification, In_quarantine, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 29
     * Try to add a new state (In_use -> In_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_use"
     * Expected Result: Receiving an error :
     *                                         "You can't only go in waiting for referencing, Under_verification, In_quarantine, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_3_to_3()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, Under_verification, In_quarantine, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 30
     * Try to add a new state (In_use -> Under_verification)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_verification"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_3_to_4()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 31
     * Try to add a new state (In_use -> In_quarantine)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_quarantine"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_3_to_5()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 32
     * Try to add a new state (In_use -> Under_repair)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_repair"
     * Expected Result: Receiving an error :
     *                                        "You can't only go in waiting for referencing, Under_verification, In_quarantine, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_3_to_6()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, Under_verification, In_quarantine, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 33
     * Try to add a new state (In_use -> Broken)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Broken"
     * Expected Result: Receiving an error :
     *                                        "You can't only go in waiting for referencing, Under_verification, In_quarantine, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_3_to_7()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, Under_verification, In_quarantine, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 34
     * Try to add a new state (In_use -> Downgraded)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Downgraded"
     * Expected Result: Receiving an error :
     *                                        "You can't only go in waiting for referencing, Under_verification, In_quarantine, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_3_to_8()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, Under_verification, In_quarantine, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 35
     * Try to add a new state (In_use -> Reformed)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Reformed"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_3_to_9()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 36
     * Try to add a new state (In_use -> Lost)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Lost"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_3_to_10()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 37
     * Try to add a new state (Under_verification -> Waiting_for_referencing)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_referencing"
     * Expected Result: Receiving an error :
     *                                        "You can't only go in In_use, In_quarantine and lost states from this one"
     * @returns void
     */
    public function test_add_state_4_to_1()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, In_quarantine and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 38
     * Try to add a new state (Under_verification -> Waiting_to_be_in_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_to_be_in_use"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, In_quarantine and lost states from this one"
     * @returns void
     */
    public function test_add_state_4_to_2()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, In_quarantine and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 39
     * Try to add a new state (Under_verification -> In_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_use"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_4_to_3()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 40
     * Try to add a new state (Under_verification -> Under_verification)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_verification"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, In_quarantine and lost states from this one"
     * @returns void
     */
    public function test_add_state_4_to_4()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, In_quarantine and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 42
     * Try to add a new state (Under_verification -> Under_repair)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_repair"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, In_quarantine and lost states from this one"
     * @returns void
     */
    public function test_add_state_4_to_6()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, In_quarantine and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 43
     * Try to add a new state (Under_verification -> Broken)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Broken"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, In_quarantine and lost states from this one"
     * @returns void
     */
    public function test_add_state_4_to_7()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, In_quarantine and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 44
     * Try to add a new state (Under_verification -> Downgraded)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Downgraded"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, In_quarantine and lost states from this one"
     * @returns void
     */
    public function test_add_state_4_to_8()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, In_quarantine and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 45
     * Try to add a new state (Under_verification -> Reformed)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Reformed"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, In_quarantine and lost states from this one"
     * @returns void
     */
    public function test_add_state_4_to_9()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, In_quarantine and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 46
     * Try to add a new state (Under_verification -> Lost)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Lost"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_4_to_10()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 47
     * Try to add a new state (In_quarantine -> Waiting_for_referencing)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_referencing"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, Under_verification, Under_repair, Broken, Downgraded, Reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_5_to_1()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, Under_verification, Under_repair, Broken, Downgraded, Reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 41
     * Try to add a new state (Under_verification -> In_quarantine)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_quarantine"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_4_to_5()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 48
     * Try to add a new state (In_quarantine -> Waiting_to_be_in_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_to_be_in_use"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, Under_verification, Under_repair, Broken, Downgraded, Reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_5_to_2()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, Under_verification, Under_repair, Broken, Downgraded, Reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 49
     * Try to add a new state (In_quarantine -> In_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_use"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_5_to_3()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 50
     * Try to add a new state (In_quarantine -> Under_verification)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_verification"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_5_to_4()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 51
     * Try to add a new state (In_quarantine -> In_quarantine)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_quarantine"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, Under_verification, Under_repair, Broken, Downgraded, Reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_5_to_5()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, Under_verification, Under_repair, Broken, Downgraded, Reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 52
     * Try to add a new state (In_quarantine -> Under_repair)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_repair"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_5_to_6()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 53
     * Try to add a new state (In_quarantine -> Broken)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Broken"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_5_to_7()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 54
     * Try to add a new state (In_quarantine -> Downgraded)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Downgraded"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_5_to_8()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 55
     * Try to add a new state (In_quarantine -> Reformed)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Reformed"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_5_to_9()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 56
     * Try to add a new state (In_quarantine -> Lost)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Lost"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_5_to_10()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 57
     * Try to add a new state (Under_repair -> Waiting_for_referencing)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_referencing"
     * Expected Result: Receive an error:
     *                                      "You can't only go in In_use, Under_verification, Broken, Downgraded and lost states from this one"
     * @returns void
     */
    public function test_add_state_6_to_1()
    {
        $this->test_add_state_5_to_6();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, Under_verification, Broken, Downgraded and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 58
     * Try to add a new state (Under_repair -> Waiting_to_be_in_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_to_be_in_use"
     * Expected Result: Receive an error:
     *                                      "You can't only go in In_use, Under_verification, Broken, Downgraded and lost states from this one"
     * @returns void
     */
    public function test_add_state_6_to_2()
    {
        $this->test_add_state_5_to_6();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, Under_verification, Broken, Downgraded and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 59
     * Try to add a new state (Under_repair -> In_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_use"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_6_to_3()
    {
        $this->test_add_state_5_to_6();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 60
     * Try to add a new state (Under_repair -> Under_verification)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_verification"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_6_to_4()
    {
        $this->test_add_state_5_to_6();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 61
     * Try to add a new state (Under_repair -> In_quarantine)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_quarantine"
     * Expected Result: Receive an error:
     *                                      "You can't only go in In_use, Under_verification, Broken, Downgraded and lost states from this one"
     * @returns void
     */
    public function test_add_state_6_to_5()
    {
        $this->test_add_state_5_to_6();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, Under_verification, Broken, Downgraded and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 62
     * Try to add a new state (Under_repair -> Under_repair)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_repair"
     * Expected Result: Receive an error:
     *                                      "You can't only go in In_use, Under_verification, Broken, Downgraded and lost states from this one"
     * @returns void
     */
    public function test_add_state_6_to_6()
    {
        $this->test_add_state_5_to_6();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, Under_verification, Broken, Downgraded and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 63
     * Try to add a new state (Under_repair -> Broken)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Broken"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_6_to_7()
    {
        $this->test_add_state_5_to_6();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 64
     * Try to add a new state (Under_repair -> Downgraded)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Downgraded"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_6_to_8()
    {
        $this->test_add_state_5_to_6();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 65
     * Try to add a new state (Under_repair -> Reformed)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Reformed"
     * Expected Result: Receive an error:
     *                                      "You can't only go in In_use, Under_verification, Broken, Downgraded and lost states from this one"
     * @returns void
     */
    public function test_add_state_6_to_9()
    {
        $this->test_add_state_5_to_6();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, Under_verification, Broken, Downgraded and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 66
     * Try to add a new state (Under_repair -> Lost)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Lost"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_6_to_10()
    {
        $this->test_add_state_5_to_6();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 67
     * Try to add a new state (Broken -> Waiting_for_referencing)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_referencing"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_7_to_1()
    {
        $this->test_add_state_6_to_7();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 68
     * Try to add a new state (Broken -> Waiting_to_be_in_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_to_be_in_use"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_7_to_2()
    {
        $this->test_add_state_6_to_7();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 69
     * Try to add a new state (Broken -> In_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_use"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_7_to_3()
    {
        $this->test_add_state_6_to_7();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 70
     * Try to add a new state (Broken -> Under_verification)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_verification"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_7_to_4()
    {
        $this->test_add_state_6_to_7();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 71
     * Try to add a new state (Broken -> In_quarantine)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_quarantine"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_7_to_5()
    {
        $this->test_add_state_6_to_7();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 72
     * Try to add a new state (Broken -> Under_repair)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_repair"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_7_to_6()
    {
        $this->test_add_state_6_to_7();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 73
     * Try to add a new state (Broken -> Broken)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Broken"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_7_to_7()
    {
        $this->test_add_state_6_to_7();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 74
     * Try to add a new state (Broken -> Downgraded)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Downgraded"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_7_to_8()
    {
        $this->test_add_state_6_to_7();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 75
     * Try to add a new state (Broken -> Reformed)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Reformed"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_7_to_9()
    {
        $this->test_add_state_6_to_7();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 76
     * Try to add a new state (Broken -> Lost)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Lost"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_7_to_10()
    {
        $this->test_add_state_6_to_7();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 77
     * Try to add a new state (Downgraded -> Waiting_for_referencing)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_referencing"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_8_to_1()
    {
        $this->test_add_state_6_to_8();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 78
     * Try to add a new state (Downgraded -> Waiting_to_be_in_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_to_be_in_use"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_8_to_2()
    {
        $this->test_add_state_6_to_8();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 79
     * Try to add a new state (Downgraded -> In_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_use"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_8_to_3()
    {
        $this->test_add_state_6_to_8();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 80
     * Try to add a new state (Downgraded -> Under_verification)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_verification"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_8_to_4()
    {
        $this->test_add_state_6_to_8();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 81
     * Try to add a new state (Downgraded -> In_quarantine)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_quarantine"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_8_to_5()
    {
        $this->test_add_state_6_to_8();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 82
     * Try to add a new state (Downgraded -> Under_repair)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_repair"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_8_to_6()
    {
        $this->test_add_state_6_to_8();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 83
     * Try to add a new state (Downgraded -> Broken)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Broken"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_8_to_7()
    {
        $this->test_add_state_6_to_8();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 84
     * Try to add a new state (Downgraded -> Downgraded)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Downgraded"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_8_to_8()
    {
        $this->test_add_state_6_to_8();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 85
     * Try to add a new state (Downgraded -> Reformed)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Reformed"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_8_to_9()
    {
        $this->test_add_state_5_to_8();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 86
     * Try to add a new state (Downgraded -> Lost)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Lost"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_8_to_10()
    {
        $this->test_add_state_5_to_8();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 87
     * Try to add a new state (Reformed -> Waiting_for_referencing)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_referencing"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_9_to_1()
    {
        $this->test_add_state_5_to_9();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 88
     * Try to add a new state (Reformed -> Waiting_to_be_in_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_to_be_in_use"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_9_to_2()
    {
        $this->test_add_state_5_to_9();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 89
     * Try to add a new state (Reformed -> In_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_use"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_9_to_3()
    {
        $this->test_add_state_5_to_9();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 90
     * Try to add a new state (Reformed -> Under_verification)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_verification"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_9_to_4()
    {
        $this->test_add_state_5_to_9();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 91
     * Try to add a new state (Reformed -> In_quarantine)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_quarantine"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_9_to_5()
    {
        $this->test_add_state_5_to_9();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 92
     * Try to add a new state (Reformed -> Under_repair)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_repair"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_9_to_6()
    {
        $this->test_add_state_5_to_9();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 93
     * Try to add a new state (Reformed -> Broken)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Broken"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_9_to_7()
    {
        $this->test_add_state_5_to_9();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 94
     * Try to add a new state (Reformed -> Downgraded)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Downgraded"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_9_to_8()
    {
        $this->test_add_state_5_to_9();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 95
     * Try to add a new state (Reformed -> Reformed)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Reformed"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_9_to_9()
    {
        $this->test_add_state_5_to_9();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 96
     * Try to add a new state (Reformed -> Lost)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Lost"
     * Expected Result: Receive an error:
     *                                      "You can't go in another state"
     * @returns void
     */
    public function test_add_state_9_to_10()
    {
        $this->test_add_state_5_to_9();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t go in another state'
        ]);
    }

    /**
     * Test Conception Number: 97
     * Try to add a new state (Lost -> Waiting_for_referencing)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_referencing"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_10_to_1()
    {
        $this->test_add_state_5_to_10();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in Waiting_to_be_in_use, Under_verification, In_use, In_quarantine and Reformed states from this one'
        ]);
    }

    /**
     * Test Conception Number: 98
     * Try to add a new state (Lost -> Waiting_to_be_in_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_to_be_in_use"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_10_to_2()
    {
        $this->test_add_state_5_to_10();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_to_be_in_use',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 99
     * Try to add a new state (Lost -> In_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_use"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_10_to_3()
    {
        $this->test_add_state_5_to_10();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 100
     * Try to add a new state (Lost -> Under_verification)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_verification"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_10_to_4()
    {
        $this->test_add_state_5_to_10();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_verification',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 101
     * Try to add a new state (Lost -> In_quarantine)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_quarantine"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_10_to_5()
    {
        $this->test_add_state_5_to_10();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_quarantine',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 102
     * Try to add a new state (Lost -> Under_repair)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_repair"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_10_to_6()
    {
        $this->test_add_state_5_to_10();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in Waiting_to_be_in_use, Under_verification, In_use, In_quarantine and Reformed states from this one'
        ]);
    }

    /**
     * Test Conception Number: 103
     * Try to add a new state (Lost -> Broken)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Broken"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_10_to_7()
    {
        $this->test_add_state_5_to_10();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in Waiting_to_be_in_use, Under_verification, In_use, In_quarantine and Reformed states from this one'
        ]);
    }

    /**
     * Test Conception Number: 104
     * Try to add a new state (Lost -> Downgraded)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Downgraded"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_10_to_8()
    {
        $this->test_add_state_5_to_10();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in Waiting_to_be_in_use, Under_verification, In_use, In_quarantine and Reformed states from this one'
        ]);
    }

    /**
     * Test Conception Number: 105
     * Try to add a new state (Lost -> Reformed)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Reformed"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_10_to_9()
    {
        $this->test_add_state_5_to_10();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_mme_temp_state', [
            'mmeTemp_id' => MmeTemp::all()->last()->id,
            'mme_state_Id' => mmeState::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 106
     * Try to add a new state (Lost -> Lost)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Lost"
     * Expected Result: The state is added in the database and linked to the mme
     * @returns void
     */
    public function test_add_state_10_to_10()
    {
        $this->test_add_state_5_to_10();
        $date = Carbon::now();
        $response = $this->post('/mme_state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'mme_id' => Mme::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in Waiting_to_be_in_use, Under_verification, In_use, In_quarantine and Reformed states from this one'
        ]);
    }
}
