<?php

namespace Tests\Feature;

use App\Models\SW01\EnumEquipmentMassUnit;
use App\Models\SW01\EnumEquipmentType;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\State;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StateTest extends TestCase
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

        $response = $this->post('/state/verif', [
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

        $response = $this->post('/state/verif', [
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

        $response = $this->post('/state/verif', [
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
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'user_id' => $user_id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You must choose a name for your state',
        ]);
    }

    /**
     * Test Conception Number: 4
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
        $response = $this->post('/state/verif', [
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
     * Test Conception Number: 5
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

        $response = $this->post('/state/verif', [
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
     * Test Conception Number: 6
     * Try to add a new state with an incorrect name
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Name"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting for installation state from this one"
     * @returns void
     */
    public function test_add_state_incorrect_name()
    {
        $eq_id = $this->create_equipment('three');
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Name',
            'state_startDate' => Carbon::now(),
            'eq_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for installation state from this one',
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
            'createdBy_id' => $user_id,
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
     * Corresponding state with each number :
     * Effective for each following tests
     * 1 : 'Waiting_for_referencing',
     * 2 :'Waiting_for_installation',
     * 3 : 'In_use',
     * 4 : 'Under_maintenance',
     * 5 : 'On_hold',
     * 6 : 'Under_repair',
     * 7 : 'Broken',
     * 8 : 'Downgraded',
     * 9 : 'Reformed',
     * 10 : 'Lost'
     */

    /**
     * Test Conception Number: 7
     * Try to add a new state (Waiting_for_referencing -> Waiting_for_referencing)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_installation"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting for installation state from this one"
     * @returns void
     */
    public function test_add_state_1_to_1()
    {
        $eq_id = $this->create_equipment('three');
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'eq_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for installation state from this one',
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
     *                                          "You can't only go in waiting for installation state from this one"
     * @returns void
     */
    public function test_add_state_1_to_3()
    {
        $eq_id = $this->create_equipment('three');
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'eq_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for installation state from this one',
        ]);
    }

    /**
     * Test Conception Number: 10
     * Try to add a new state (Waiting_for_referencing -> Under_maintenance)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_maintenance"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting for installation state from this one"
     * @returns void
     */
    public function test_add_state_1_to_4()
    {
        $eq_id = $this->create_equipment('three');
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_maintenance',
            'state_startDate' => $date,
            'eq_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for installation state from this one',
        ]);
    }

    /**
     * Test Conception Number: 11
     * Try to add a new state (Waiting_for_referencing -> On_hold)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "On_hold"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting for installation state from this one"
     * @returns void
     */
    public function test_add_state_1_to_5()
    {
        $eq_id = $this->create_equipment('three');
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'On_hold',
            'state_startDate' => $date,
            'eq_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for installation state from this one',
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
     *                                          "You can't only go in waiting for installation state from this one"
     * @returns void
     */
    public function test_add_state_1_to_6()
    {
        $eq_id = $this->create_equipment('three');
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'eq_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for installation state from this one',
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
     *                                          "You can't only go in waiting for installation state from this one"
     * @returns void
     */
    public function test_add_state_1_to_7()
    {
        $eq_id = $this->create_equipment('three');
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'eq_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for installation state from this one',
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
     *                                          "You can't only go in waiting for installation state from this one"
     * @returns void
     */
    public function test_add_state_1_to_8()
    {
        $eq_id = $this->create_equipment('three');
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'eq_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for installation state from this one',
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
     *                                          "You can't only go in waiting for installation state from this one"
     * @returns void
     */
    public function test_add_state_1_to_9()
    {
        $eq_id = $this->create_equipment('three');
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'eq_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for installation state from this one',
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
     *                                          "You can't only go in waiting for installation state from this one"
     * @returns void
     */
    public function test_add_state_1_to_10()
    {
        $eq_id = $this->create_equipment('three');
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Destroyed',
            'state_startDate' => $date,
            'eq_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for installation state from this one',
        ]);
    }

    /**
     * Test Conception Number: 17
     * Try to add a new state (Waiting_for_installation -> Waiting_for_referencing)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_referencing"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_2_to_1()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 8
     * Try to add a new state (Waiting_for_referencing -> Waiting_for_installation)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_installation"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting for installation state from this one"
     * @returns void
     */
    public function test_add_state_1_to_2()
    {
        $eq_id = $this->create_equipment('three');
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_installation',
            'state_startDate' => $date,
            'eq_id' => $eq_id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_installation',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_installation',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 18
     * Try to add a new state (Waiting_for_installation -> Waiting_for_installation)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_installation"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in waiting for referencing, in use, on hold, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_2_to_2()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_installation',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, in use, on hold, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 19
     * Try to add a new state (Waiting_for_installation -> In_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_use"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_2_to_3()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 20
     * Try to add a new state (Waiting_for_installation -> Under_maintenance)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_maintenance"
     * Expected Result: Receiving an error :
     *                                         "You can't only go in waiting for referencing, in use, on hold, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_2_to_4()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_maintenance',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, in use, on hold, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 21
     * Try to add a new state (Waiting_for_installation -> On_hold)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "On_hold"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_2_to_5()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'On_hold',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'On_hold',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'On_hold',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 22
     * Try to add a new state (Waiting_for_installation -> Under_repair)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_repair"
     * Expected Result: Receiving an error :
     *                                         "You can't only go in waiting for referencing, in use, on hold, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_2_to_6()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, in use, on hold, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 23
     * Try to add a new state (Waiting_for_installation -> Broken)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Broken"
     * Expected Result: Receiving an error :
     *                                         "You can't only go in waiting for referencing, in use, on hold, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_2_to_7()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, in use, on hold, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 24
     * Try to add a new state (Waiting_for_installation -> Downgraded)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Downgraded"
     * Expected Result: Receiving an error :
     *                                         "You can't only go in waiting for referencing, in use, on hold, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_2_to_8()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, in use, on hold, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 25
     * Try to add a new state (Waiting_for_installation -> Reformed)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Reformed"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_2_to_9()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 26
     * Try to add a new state (Waiting_for_installation -> Lost)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Lost"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_2_to_10()
    {
        $this->test_add_state_1_to_2();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
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
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_3_to_1()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 28
     * Try to add a new state (In_use -> Waiting_for_installation)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_installation"
     * Expected Result: Receiving an error :
     *                                         "You can't only go in waiting for referencing, Under_maintenance, on hold, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_3_to_2()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_installation',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, Under_maintenance, on hold, reformed and lost states from this one',
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
     *                                         "You can't only go in waiting for referencing, Under_maintenance, on hold, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_3_to_3()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, Under_maintenance, on hold, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 31
     * Try to add a new state (In_use -> On_hold)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "On_hold"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_3_to_5()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'On_hold',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'On_hold',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'On_hold',
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
     *                                        "You can't only go in waiting for referencing, Under_maintenance, on hold, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_3_to_6()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, Under_maintenance, on hold, reformed and lost states from this one',
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
     *                                        "You can't only go in waiting for referencing, Under_maintenance, on hold, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_3_to_7()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, Under_maintenance, on hold, reformed and lost states from this one',
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
     *                                        "You can't only go in waiting for referencing, Under_maintenance, on hold, reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_3_to_8()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in waiting for referencing, Under_maintenance, on hold, reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 35
     * Try to add a new state (In_use -> Reformed)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Reformed"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_3_to_9()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
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
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_3_to_10()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 37
     * Try to add a new state (Under_maintenance -> Waiting_for_referencing)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_referencing"
     * Expected Result: Receiving an error :
     *                                        "You can't only go in In_use, On_hold and lost states from this one"
     * @returns void
     */
    public function test_add_state_4_to_1()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, On_hold and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 30
     * Try to add a new state (In_use -> Under_maintenance)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_maintenance"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_3_to_4()
    {
        $this->test_add_state_2_to_3();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_maintenance',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_maintenance',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_maintenance',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 38
     * Try to add a new state (Under_maintenance -> Waiting_for_installation)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_installation"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, On_hold and lost states from this one"
     * @returns void
     */
    public function test_add_state_4_to_2()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_installation',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, On_hold and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 39
     * Try to add a new state (Under_maintenance -> In_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_use"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_4_to_3()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 40
     * Try to add a new state (Under_maintenance -> Under_maintenance)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_maintenance"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, On_hold and lost states from this one"
     * @returns void
     */
    public function test_add_state_4_to_4()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_maintenance',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, On_hold and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 42
     * Try to add a new state (Under_maintenance -> Under_repair)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_repair"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, On_hold and lost states from this one"
     * @returns void
     */
    public function test_add_state_4_to_6()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, On_hold and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 43
     * Try to add a new state (Under_maintenance -> Broken)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Broken"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, On_hold and lost states from this one"
     * @returns void
     */
    public function test_add_state_4_to_7()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, On_hold and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 44
     * Try to add a new state (Under_maintenance -> Downgraded)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Downgraded"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, On_hold and lost states from this one"
     * @returns void
     */
    public function test_add_state_4_to_8()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, On_hold and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 45
     * Try to add a new state (Under_maintenance -> Reformed)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Reformed"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, On_hold and lost states from this one"
     * @returns void
     */
    public function test_add_state_4_to_9()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, On_hold and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 46
     * Try to add a new state (Under_maintenance -> Lost)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Lost"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_4_to_10()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 47
     * Try to add a new state (On_hold -> Waiting_for_referencing)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_referencing"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, Under_maintenance, Under_repair, Broken, Downgraded, Reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_5_to_1()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_referencing',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, Under_maintenance, Under_repair, Broken, Downgraded, Reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 41
     * Try to add a new state (Under_maintenance -> On_hold)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "On_hold"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_4_to_5()
    {
        $this->test_add_state_3_to_4();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'On_hold',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'On_hold',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'On_hold',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 48
     * Try to add a new state (On_hold -> Waiting_for_installation)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Waiting_for_installation"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, Under_maintenance, Under_repair, Broken, Downgraded, Reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_5_to_2()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Waiting_for_installation',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, Under_maintenance, Under_repair, Broken, Downgraded, Reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 49
     * Try to add a new state (On_hold -> In_use)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "In_use"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_5_to_3()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'In_use',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 50
     * Try to add a new state (On_hold -> Under_maintenance)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_maintenance"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_5_to_4()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_maintenance',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_maintenance',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_maintenance',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 51
     * Try to add a new state (On_hold -> On_hold)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "On_hold"
     * Expected Result: Receiving an error :
     *                                          "You can't only go in In_use, Under_maintenance, Under_repair, Broken, Downgraded, Reformed and lost states from this one"
     * @returns void
     */
    public function test_add_state_5_to_5()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'On_hold',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'state_name' => 'You can\'t only go in In_use, Under_maintenance, Under_repair, Broken, Downgraded, Reformed and lost states from this one',
        ]);
    }

    /**
     * Test Conception Number: 52
     * Try to add a new state (On_hold -> Under_repair)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Under_repair"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_5_to_6()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Under_repair',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 53
     * Try to add a new state (On_hold -> Broken)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Broken"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_5_to_7()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Broken',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 54
     * Try to add a new state (On_hold -> Downgraded)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Downgraded"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_5_to_8()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Downgraded',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 55
     * Try to add a new state (On_hold -> Reformed)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Reformed"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_5_to_9()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Reformed',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }

    /**
     * Test Conception Number: 56
     * Try to add a new state (On_hold -> Lost)
     * Remarks: "Remarks"
     * Start Date: Today
     * End Date: /
     * Name: "Lost"
     * Expected Result: The state is added in the database and linked to the equipment
     * @returns void
     */
    public function test_add_state_5_to_10()
    {
        $this->test_add_state_4_to_5();
        $date = Carbon::now();
        $response = $this->post('/state/verif', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date,
            'eq_id' => Equipment::all()->last()->id,
            'user_id' => user::all()->last()->id,
            'state_validate' => 'drafted',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->last()->id,
            'state_id' => State::all()->last()->id,
        ]);
        $this->assertDatabaseHas('states', [
            'state_remarks' => 'Remarks',
            'state_name' => 'Lost',
            'state_startDate' => $date->format('Y-m-d'),
            'state_validate' => 'drafted',
        ]);
    }
}
