<?php

namespace Tests\Feature;

use App\Models\SW01\EnumEquipmentMassUnit;
use App\Models\SW01\EnumEquipmentType;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\Mme;
use App\Models\SW01\MmeState;
use App\Models\SW01\MmeTemp;
use App\Models\SW01\State;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurativeMaintenanceOperationTest extends TestCase
{
    use RefreshDatabase;

    public function create_equipment($name, $validated = 'drafted')
    {
        $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
        $response = $this->post('/equipment/enum/massUnit/add', [
            'value' => $name,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        $countEqType = EnumEquipmentType::all()->count();
        $response = $this->post('/equipment/enum/type/add', [
            'value' => $name,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countEqType + 1, EnumEquipmentType::all());
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
            'eq_massUnit' => $name
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
            'eq_massUnit' => $name
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment + 1, Equipment::all()->count());
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_version' => 1,
            'eqTemp_location' => $name,
            'eqTemp_validate' => $validated,
            'eqTemp_lifeSheetCreated' => 0,
            'eqTemp_mass' => 1234,
            'eqTemp_remarks' => $name,
            'eqTemp_mobility' => null,
            'enumType_id' => EnumEquipmentType::all()->where('value', '=', $name)->first()->id,
            'enumMassUnit_id' => EnumEquipmentMassUnit::all()->where('value', '=', $name)->first()->id,
        ]);
        $this->assertDatabaseHas('pivot_equipment_temp_state', [
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', Equipment::all()->last()->id)->last()->id,
        ]);
        return Equipment::all()->last()->id;
    }

    public function create_mme($name, $validated = 'drafted')
    {
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
     * Test Conception Number: 1
     * Add a curative maintenance operation as drafted with no values
     * Description: /
     * Report Number : /
     * Start Date : /
     * End Date : /
     * Expected Result: Receiving an error :
     *                                          "You must enter a description for the curative maintenance operation"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_drafted_with_no_values()
    {
        $eq_id = $this->create_equipment('test');

        /*$mostRecentlyMmeTmp = MmeTemp::where('mme_id', '=', $mme_id)->orderBy('created_at', 'desc')->first();
        $states=$mostRecentlyMmeTmp->states;
        $mostRecentlyState=MmeState::orderBy('created_at', 'asc')->first();
        foreach($states as $state){
            $date=$state->created_at ;
            $date2=$mostRecentlyState->created_at;
            if ($date>=$date2){
                $mostRecentlyState=$state ;
            }
        }*/

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states=$mostRecentlyEqTmp->states;
        $mostRecentlyState=State::orderBy('created_at', 'asc')->first();
        foreach($states as $state){
            $date=$state->created_at ;
            $date2=$mostRecentlyState->created_at;
            if ($date>=$date2){
                $mostRecentlyState=$state ;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'drafted',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter a description for the curative maintenance operation'
        ]);
    }

    /**
     * Test Conception Number: 2
     * Add a curative maintenance operation as drafted with a too short description
     * Description: "in"
     * Report Number : /
     * Start Date : /
     * End Date : /
     * Expected Result: Receiving an error :
     *                                          "You must enter at least three characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_drafted_with_short_desc()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states=$mostRecentlyEqTmp->states;
        $mostRecentlyState=State::orderBy('created_at', 'asc')->first();
        foreach($states as $state){
            $date=$state->created_at ;
            $date2=$mostRecentlyState->created_at;
            if ($date>=$date2){
                $mostRecentlyState=$state ;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_description' => 'in',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 3
     * Add a curative maintenance operation as drafted with a too long description
     * Description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Report Number : /
     * Start Date : /
     * End Date : /
     * Expected Result: Receiving an error :
     *                                          "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_drafted_with_long_desc()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states=$mostRecentlyEqTmp->states;
        $mostRecentlyState=State::orderBy('created_at', 'asc')->first();
        foreach($states as $state){
            $date=$state->created_at ;
            $date2=$mostRecentlyState->created_at;
            if ($date>=$date2){
                $mostRecentlyState=$state ;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 4
     * Add a curative maintenance operation as drafted with a too long report number
     * Description: "three"
     * Report Number : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Start Date : /
     * End Date : /
     * Expected Result: Receiving an error :
     *                                          "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_drafted_with_long_report_number()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyState->created_at;
            if ($date >= $date2) {
                $mostRecentlyState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a maximum of 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 4
     * Add a curative maintenance operation as drafted with correct values
     * Description: "three"
     * Report Number : "three"
     * Start Date : /
     * End Date : /
     * Expected Result: The curative maintenance operation is added to the database
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_drafted_with_correct_values()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyState->created_at;
            if ($date >= $date2) {
                $mostRecentlyState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'drafted',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_validate' => 'drafted',
        ]);
    }


    /**
     * Test Conception Number: 6
     * Add a curative maintenance operation as to be validated with no values
     * Description: /
     * Report Number : /
     * Start Date : /
     * End Date : /
     * Expected Result: Receiving an error :
     *                                          "You must enter a description for the curative maintenance operation"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_to_be_validated_with_no_values()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states=$mostRecentlyEqTmp->states;
        $mostRecentlyState=State::orderBy('created_at', 'asc')->first();
        foreach($states as $state){
            $date=$state->created_at ;
            $date2=$mostRecentlyState->created_at;
            if ($date>=$date2){
                $mostRecentlyState=$state ;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'to_be_validated',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter a description for the curative maintenance operation'
        ]);
    }

    /**
     * Test Conception Number: 7
     * Add a curative maintenance operation as to be validated with a too short description
     * Description: "in"
     * Report Number : /
     * Start Date : /
     * End Date : /
     * Expected Result: Receiving an error :
     *                                          "You must enter at least three characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_to_be_validated_with_short_desc()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states=$mostRecentlyEqTmp->states;
        $mostRecentlyState=State::orderBy('created_at', 'asc')->first();
        foreach($states as $state){
            $date=$state->created_at ;
            $date2=$mostRecentlyState->created_at;
            if ($date>=$date2){
                $mostRecentlyState=$state ;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'in',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 8
     * Add a curative maintenance operation as to be validated with a too long description
     * Description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Report Number : /
     * Start Date : /
     * End Date : /
     * Expected Result: Receiving an error :
     *                                          "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_to_be_validated_with_long_desc()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states=$mostRecentlyEqTmp->states;
        $mostRecentlyState=State::orderBy('created_at', 'asc')->first();
        foreach($states as $state){
            $date=$state->created_at ;
            $date2=$mostRecentlyState->created_at;
            if ($date>=$date2){
                $mostRecentlyState=$state ;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_description' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 9
     * Add a curative maintenance operation as to be validated with a too long report number
     * Description: "three"
     * Report Number : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Start Date : /
     * End Date : /
     * Expected Result: Receiving an error :
     *                                          "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_to_be_validated_with_long_report_number()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyState->created_at;
            if ($date >= $date2) {
                $mostRecentlyState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a maximum of 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 10
     * Add a curative maintenance operation as to be validated with correct values
     * Description: "three"
     * Report Number : "three"
     * Start Date : /
     * End Date : /
     * Expected Result: The curative maintenance operation is added to the database
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_to_be_validated_with_correct_values()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyState->created_at;
            if ($date >= $date2) {
                $mostRecentlyState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/state/curMtnOp/', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'to_be_validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('curative_maintenance_operations', [
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'state_id' => $mostRecentlyState->id,
        ]);
    }

    /**
     * Test Conception Number: 11
     * Add a curative maintenance operation as validated with no values
     * Description: /
     * Report Number : /
     * Start Date : /
     * End Date : /
     * Expected Result: Receiving an error :
     *                                          "You must enter a report number for the curative maintenance operation"
     *                                          "You must enter a description for the curative maintenance operation"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_no_values()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyState->created_at;
            if ($date >= $date2) {
                $mostRecentlyState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'validated',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a report number for the curative maintenance operation',
            'curMtnOp_description' => 'You must enter a description for the curative maintenance operation'
        ]);
    }

    /**
     * Test Conception Number: 12
     * Add a curative maintenance operation as validated with a too short description
     * Description: "in"
     * Report Number : /
     * Start Date : /
     * End Date : /
     * Expected Result: Receiving an error :
     *                                          "You must enter a report number for the curative maintenance operation"
     *                                          "You must enter at least three characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_short_description()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyState->created_at;
            if ($date >= $date2) {
                $mostRecentlyState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'in',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a report number for the curative maintenance operation',
            'curMtnOp_description' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 13
     * Add a curative maintenance operation as validated with a too long description
     * Description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Report Number : /
     * Start Date : /
     * End Date : /
     * Expected Result: Receiving an error :
     *                                          "You must enter a report number for the curative maintenance operation"
     *                                          "You must enter a maximum of 50 characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_long_description()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyState->created_at;
            if ($date >= $date2) {
                $mostRecentlyState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a report number for the curative maintenance operation',
            'curMtnOp_description' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number: 14
     * Add a curative maintenance operation as validated with a too short report number
     * Description: "three"
     * Report Number : "in"
     * Start Date : /
     * End Date : /
     * Expected Result: Receiving an error :
     *                                          "You must enter at least three characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_short_report_number()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyState->created_at;
            if ($date >= $date2) {
                $mostRecentlyState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'in',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 15
     * Add a curative maintenance operation as validated with a too long report number
     * Description: "three"
     * Report Number : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non "
     * Start Date : /
     * End Date : /
     * Expected Result: Receiving an error :
     *                                          "You must enter a maximum of 255 characters"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_long_report_number()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyState->created_at;
            if ($date >= $date2) {
                $mostRecentlyState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non ',
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'curMtnOp_reportNumber' => 'You must enter a maximum of 255 characters'
        ]);
    }

    /**
     * Test Conception Number: 16
     * Add a curative maintenance operation as validated with correct values but no start date
     * Description: "three"
     * Report Number : "three"
     * Start Date : /
     * End Date : /
     * Expected Result: Receiving an error :
     *                                          "You have to entered the startDate of your curative maintenance operation for validate it"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_correct_values()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyState->created_at;
            if ($date >= $date2) {
                $mostRecentlyState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_startDate' => 'You have to entered the startDate of your curative maintenance operation for validate it'
        ]);
    }

    /**
     * Test Conception Number: 17
     * Add a curative maintenance operation as validated with correct values but no end date
     * Description: "three"
     * Report Number : "three"
     * Start Date : Today
     * End Date : /
     * Expected Result: Receiving an error :
     *                                          "You have to entered the endDate of your curative maintenance operation for validate it"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_correct_values_but_no_end_date()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyState->created_at;
            if ($date >= $date2) {
                $mostRecentlyState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_startDate' => Carbon::now(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_endDate' => 'You have to entered the endDate of your curative maintenance operation for validate it'
        ]);
    }

    /**
     * Test Conception Number: 18
     * Add a curative maintenance operation as validated with correct values and dates
     * Description: "three"
     * Report Number : "three"
     * Start Date : Today
     * End Date : Today + 1 week
     * Expected Result: Receiving an error :
     *                                          "You have to entered the realizator of this curative maintenance operation for validate it"
     * @returns void
     */
    public function test_add_curative_maintenance_operation_as_validated_with_correct_values_and_dates()
    {
        $eq_id = $this->create_equipment('test');

        $mostRecentlyEqTmp = EquipmentTemp::where('equipment_id', '=', $eq_id)->orderBy('created_at', 'desc')->first();
        $states = $mostRecentlyEqTmp->states;
        $mostRecentlyState = State::orderBy('created_at', 'asc')->first();
        foreach ($states as $state) {
            $date = $state->created_at;
            $date2 = $mostRecentlyState->created_at;
            if ($date >= $date2) {
                $mostRecentlyState = $state;
            }
        }

        $response = $this->post('/curMtnOp/verif', [
            'state_id' => $mostRecentlyState->id,
            'curMtnOp_validate' => 'validated',
            'curMtnOp_description' => 'three',
            'curMtnOp_reportNumber' => 'three',
            'curMtnOp_startDate' => Carbon::now(),
            'curMtnOp_endDate' => Carbon::now()->addWeek(),
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'curMtnOp_validate' => 'You have to entered the realizator of this curative maintenance operation for validate it'
        ]);
    }
}
