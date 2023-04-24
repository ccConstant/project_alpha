<?php

/*
* Filename : PowerTest.php
* Creation date : 31 May 2022
* Update date: 14 Apr 2023
* This file contains all the tests about the power table.
* Coverage : 100%
*/

use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\Power;
use App\Models\SW01\EnumPowerType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class PowerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Conception Number: 1
     * Saved a power as drafted from add-menu with no values
     * Type: /
     * Name: /
     * Value: /
     * Unit : /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: Receiving an error: "You must enter a value for your power"
     * @return void
     */
    public function test_add_pow_drafted_addMenu_NoValue()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_name' => 'You must enter a name for the power'
        ]);
    }

    /**
     * Test Conception Number: 2
     * Saved a power as drafted from add-menu with a too short name
     * Type: /
     * Name: "in"
     * Value: /
     * Unit : /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: Receiving an error: "You must enter at least three characters"
     * @return void
     */
    public function test_add_pow_drafted_addMenu_tooShortName()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_name' => 'in'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_name' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 3
     * Saved a power as drafted from add-menu with a too long name
     * Type: /
     * Name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Value: /
     * Unit : /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: Receiving an error: "You must enter a maximum of 25 characters"
     * @return void
     */

    public function test_add_pow_drafted_addMenu_tooLongName()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_name' => 'You must enter a maximum of 25 characters'
        ]);
    }

    /**
     * Test Conception Number: 4
     * Saved a power as drafted from add menu with a correct name and a too long value
     * Type: /
     * Name: "three"
     * Value: "123456789012345678901234567890"
     * Unit : /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: Receiving an error: "You must enter a maximum of 25 characters"
     * @return void
     */
    public function test_add_pow_drafted_addMenu_correctName_tooLongValue()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_name' => 'three',
            'pow_value' => '123456789012345678901234567890'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_value' => 'You must enter a maximum of 25 characters'
        ]);
    }

    /**
     * Test Conception Number: 5
     * Saved a power as drafted from add menu with a correct name but a too long unit
     * Type: /
     * Name: "three"
     * Value: /
     * Unit : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: Receiving an error: "You must enter a maximum of 25 characters"
     * @return void
     */
    public function test_add_pow_drafted_addMenu_correctName_tooLongUnit()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_name' => 'three',
            'pow_unit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_unit' => 'You must enter a maximum of 25 characters'
        ]);
    }

    /**
     * Test Conception Number: 6
     * Saved a power as drafted from ad menu with a correct name and a too long consumption value
     * Type: /
     * Name: "three"
     * Value: /
     * Unit : /
     * Consumption Value: "123456789012345678901234567890"
     * Consumption Unit: /
     * Expected result: Receiving an error: "You must enter a maximum of 25 characters"
     * @return void
     */
    public function test_add_pow_drafted_addMenu_correctName_tooLongConsumptionValue()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_name' => 'three',
            'pow_consumptionValue' => '123456789012345678901234567890'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_consumptionValue' => 'You must enter a maximum of 25 characters'
        ]);
    }

    /**
     * Test Conception Number: 7
     * Saved a power as drafted from add menu with a correct name and a too long consumption unit
     * Type: /
     * Name: "three"
     * Value: /
     * Unit : /
     * Consumption Value: /
     * Consumption Unit: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Expected result: Receiving an error: "You must enter a maximum of 25 characters"
     * @return void
     */
    public function test_add_pow_drafted_addMenu_correctName_tooLongConsumptionUnit()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_name' => 'three',
            'pow_consumptionUnit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_consumptionUnit' => 'You must enter a maximum of 25 characters'
        ]);
    }

    /**
     * Test Conception Number 8
     * Successfully save a power as drafted in the database
     * Type: /
     * Name: "three"
     * Value: /
     * Unit: /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: The power is correctly saved in data base and correctly linked to the equipment
     * @returns void
     */
    public function test_add_pow_drafted_success_only_name()
    {
        // Add of the equipment
        $response=$this->post('equipment/verif', [
            'eq_internalReference' => 'draft1Test',
            'eq_externalReference' => 'draft1Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_internalReference' => 'draft1Test',
            'eq_externalReference' => 'draft1Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_name' => 'three'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'drafted',
            'pow_name' => 'three',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'enumPowerType_id' => null,
            'pow_name' => 'three',
            'pow_value' => null,
            'pow_unit' => null,
            'pow_consumptionValue' => null,
            'pow_consumptionUnit' => null,
            'equipmentTemp_id' => Equipment::all()->last()->id
        ]);
    }

    /**
     * Test Conception Number 9
     * Successfully save a power as drafted in the database
     * Type: Electric
     * Name: "three"
     * Value: 220
     * Unit: V
     * Consumption Value: 18
     * Consumption Unit: kwH
     * Expected result: The power is correctly saved in data base and correctly linked to the equipment
     * @returns void
     */
    public function test_add_pow_drafted_success_full()
    {
        // Add of the equipment
        $response=$this->post('equipment/verif', [
            'eq_internalReference' => 'draft2Test',
            'eq_externalReference' => 'draft2Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_internalReference' => 'draft2Test',
            'eq_externalReference' => 'draft2Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add of the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add of the power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'enumPowerType_id' => EnumPowerType::all()->last()->id,
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id
        ]);
    }

    /**
     * Test Conception Number: 10
     * Saved a power as to be validated from add menu with no values
     * Type: /
     * Name: /
     * Value: /
     * Unit : /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: Receiving an error: "You must enter a name for the power"
     * @return void
     */
    public function test_add_pow_toBeValidated_addMenu_noValues()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'to_be_validated'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_name' => 'You must enter a name for the power'
        ]);
    }

    /**
     * Test Conception Number: 11
     * Saved a power as to be validated from add menu with a too short name
     * Type: /
     * Name: "in"
     * Value: /
     * Unit : /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: Receiving an error: "You must enter a minimum of three characters"
     * @return void
     */
    public function test_add_pow_toBeValidated_addMenu_tooShortName() {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'to_be_validated',
            'pow_name' => 'in'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_name' => 'You must enter at least three characters'
        ]);
    }

    /**
     * Test Conception Number: 12
     * Saved a power as to be validated from add menu with a too long name
     * Type: /
     * Name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Value: /
     * Unit : /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: Receiving an error: "You must enter a maximum of 25 characters"
     * @return void
     */
    public function test_add_pow_toBeValidated_addMenu_tooLongName()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'to_be_validated',
            'pow_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_name' => 'You must enter a maximum of 25 characters'
        ]);
    }

    /**
     * Test Conception Number: 13
     * Saved a power as to be validated from add menu with a correct name and a too long value
     * Type: /
     * Name: "three"
     * Value: "123456789012345678901234567890"
     * Unit : /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: Receiving an error: "You must enter a maximum of 25 characters"
     * @return void
     */
    public function test_add_pow_toBeValidated_addMenu_correctName_tooLongValue()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'to_be_validated',
            'pow_name' => 'three',
            'pow_value' => '123456789012345678901234567890'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_value' => 'You must enter a maximum of 25 characters'
        ]);
    }

    /**
     * Test Conception Number: 14
     * Saved a power as to be validated from add menu with a correct name and a too long unit
     * Type: /
     * Name: "three"
     * Value: /
     * Unit : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: Receiving an error: "You must enter a maximum of 25 characters"
     * @return void
     */
    public function test_add_pow_toBeValidated_addMenu_correctName_tooLongUnit()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'to_be_validated',
            'pow_name' => 'three',
            'pow_unit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_unit' => 'You must enter a maximum of 25 characters'
        ]);
    }

    /**
     * Test Conception Number: 15
     * Saved a power as to be validated from add menu with a correct name and a too long consumption value
     * Type: /
     * Name: "three"
     * Value: /
     * Unit : /
     * Consumption Value: "123456789012345678901234567890"
     * Consumption Unit: /
     * Expected result: Receiving an error: "You must enter a maximum of 25 characters"
     * @return void
     */
    public function test_add_pow_toBeValidated_addMenu_correctName_tooLongConsumptionValue()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'to_be_validated',
            'pow_name' => 'three',
            'pow_consumptionValue' => '123456789012345678901234567890'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_consumptionValue' => 'You must enter a maximum of 25 characters'
        ]);
    }

    /**
     * Test Conception Number: 16
     * Saved a power as to be validated from add menu with a correct name and a too long consumption unit
     * Type: /
     * Name: "three"
     * Value: /
     * Unit : /
     * Consumption Value: /
     * Consumption Unit: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Expected result: Receiving an error: "You must enter a maximum of 25 characters"
     * @return void
     */
    public function test_add_pow_toBeValidated_addMenu_correctName_tooLongConsumptionUnit()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'to_be_validated',
            'pow_name' => 'three',
            'pow_consumptionUnit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_consumptionUnit' => 'You must enter a maximum of 25 characters'
        ]);
    }

    /**
     * Test Conception Number 17
     * Successfully save a power as to be validated in the database
     * Type: /
     * Name: "three"
     * Value: /
     * Unit: /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: The power is correctly saved in data base and correctly linked to the equipment
     * @returns void
     */
    public function test_add_pow_toBeValidated_success_only_name()
    {
        // Add of the equipment
        $response=$this->post('equipment/verif', [
            'eq_internalReference' => 'toBeVa1Test',
            'eq_externalReference' => 'toBeVa1Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_internalReference' => 'toBeVa1Test',
            'eq_externalReference' => 'toBeVa1Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_name' => 'three'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'to_be_validated',
            'pow_name' => 'three',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'to_be_validated',
            'enumPowerType_id' => null,
            'pow_name' => 'three',
            'pow_value' => null,
            'pow_unit' => null,
            'pow_consumptionValue' => null,
            'pow_consumptionUnit' => null,
            'equipmentTemp_id' => Equipment::all()->last()->id
        ]);
    }

    /**
     * Test Conception Number 18
     * Successfully save a power as to be validated in the database
     * Type: Electric
     * Name: "three"
     * Value: 220
     * Unit: V
     * Consumption Value: 18
     * Consumption Unit: kwH
     * Expected result: The power is correctly saved in data base and correctly linked to the equipment
     * @returns void
     */
    public function test_add_pow_toBeValidated_success_full()
    {
        // Add of the equipment
        $response=$this->post('equipment/verif', [
            'eq_internalReference' => 'toBeVa2Test',
            'eq_externalReference' => 'toBeVa2Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_internalReference' => 'toBeVa2Test',
            'eq_externalReference' => 'toBeVa2Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add of the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add of the power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'to_be_validated',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'to_be_validated',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'to_be_validated',
            'enumPowerType_id' => EnumPowerType::all()->last()->id,
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id
        ]);
    }

    /**
     * Test Conception Number: 19
     * Saved a power as validated from add menu with no values
     * Type: /
     * Name: /
     * Value: /
     * Unit : /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: Receiving an error:
     *                                      "You must enter a name for the power",
     *                                      "You must enter a value for the power",
     *                                      "You must enter a unit for the power",
     *                                      "You must enter a value for the consumption of the power",
     *                                      "You must enter a unit for the consumption of the power"
     * @return void
     */
    public function test_add_pow_validated_addMenu_noValues() {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_name' => 'You must enter a name for the power',
            'pow_value' => 'You must enter a value for the power',
            'pow_unit' => 'You must enter a unit for the power',
            'pow_consumptionValue' => 'You must enter a value for the consumption of the power',
            'pow_consumptionUnit' => 'You must enter a unit for the consumption of the power'
        ]);
    }

    /**
     * Test Conception Number: 20
     * Saved a power as validated from add menu with a too short name
     * Type: /
     * Name: "in"
     * Value: /
     * Unit : /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: Receiving an error:
     *                                      "You must enter at least three characters",
     *                                      "You must enter a value for the power",
     *                                      "You must enter a unit for the power",
     *                                      "You must enter a value for the consumption of the power",
     *                                      "You must enter a unit for the consumption of the power"
     * @return void
     */
    public function test_add_pow_validated_addMenu_tooShortName()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_name' => 'in'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_name' => 'You must enter at least three characters',
            'pow_value' => 'You must enter a value for the power',
            'pow_unit' => 'You must enter a unit for the power',
            'pow_consumptionValue' => 'You must enter a value for the consumption of the power',
            'pow_consumptionUnit' => 'You must enter a unit for the consumption of the power'
        ]);
    }

    /**
     * Test Conception Number: 21
     * Saved a power as validated from add menu with a too long name
     * Type: /
     * Name: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Value: /
     * Unit : /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: Receiving an error:
     *                                      "You must enter a maximum of 25 characters",
     *                                      "You must enter a value for the power",
     *                                      "You must enter a unit for the power",
     *                                      "You must enter a value for the consumption of the power",
     *                                      "You must enter a unit for the consumption of the power"
     * @return void
     */
    public function test_add_pow_validated_addMenu_tooLongName() {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_name' => 'You must enter a maximum of 25 characters',
            'pow_value' => 'You must enter a value for the power',
            'pow_unit' => 'You must enter a unit for the power',
            'pow_consumptionValue' => 'You must enter a value for the consumption of the power',
            'pow_consumptionUnit' => 'You must enter a unit for the consumption of the power'
        ]);
    }

    /**
     * Test Conception Number: 22
     * Saved a power as validated from ad menu with a correct name and a too long value
     * Type: /
     * Name: "three"
     * Value: "123456789012345678901234567890"
     * Unit : /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: Receiving an error:
     *                                      "You must enter a maximum of 25 characters",
     *                                      "You must enter a unit for the power",
     *                                      "You must enter a value for the consumption of the power",
     *                                      "You must enter a unit for the consumption of the power"
     * @return void
     */
    public function test_add_pow_validated_addMenu_correctName_tooLongValue()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_name' => 'three',
            'pow_value' => '123456789012345678901234567890'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_value' => 'You must enter a maximum of 25 characters',
            'pow_unit' => 'You must enter a unit for the power',
            'pow_consumptionValue' => 'You must enter a value for the consumption of the power',
            'pow_consumptionUnit' => 'You must enter a unit for the consumption of the power'
        ]);
    }

    /**
     * Test Conception Number: 23
     * Saved a power as validated from add menu with a correct name, a correct value and a too long unit
     * Type: /
     * Name: "three"
     * Value: "12345"
     * Unit : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: Receiving an error:
     *                                      "You must enter a maximum of 25 characters",
     *                                      "You must enter a value for the consumption of the power",
     *                                      "You must enter a unit for the consumption of the power"
     * @return void
     */
    public function test_add_pow_validated_addMenu_correctName_correctValue_tooLongUnit()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_name' => 'three',
            'pow_value' => '12345',
            'pow_unit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_unit' => 'You must enter a maximum of 25 characters',
            'pow_consumptionValue' => 'You must enter a value for the consumption of the power',
            'pow_consumptionUnit' => 'You must enter a unit for the consumption of the power'
        ]);
    }

    /**
     * Test Conception Number: 24
     * Saved a power as validated from add menu with a correct name, a correct value, a correct unit and a too long consumption value
     * Type: /
     * Name: "three"
     * Value: "12345"
     * Unit : "unit"
     * Consumption Value: "123456789012345678901234567890"
     * Consumption Unit: /
     * Expected result: Receiving an error:
     *                                      "You must enter a maximum of 25 characters",
     *                                      "You must enter a unit for the consumption of the power"
     * @return void
     */
    public function test_add_pow_validated_addMenu_correctName_correctValue_correctUnit_tooLongConsumptionValue() {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_name' => 'three',
            'pow_value' => '12345',
            'pow_unit' => 'unit',
            'pow_consumptionValue' => '123456789012345678901234567890'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_consumptionValue' => 'You must enter a maximum of 25 characters',
            'pow_consumptionUnit' => 'You must enter a unit for the consumption of the power'
        ]);
    }

    /**
     * Test Conception Number: 25
     * Saved a power as validated from ad menu with a correct name, a correct value, a correct unit, a correct consumption value and a too long consumption unit
     * Type: /
     * Name: "three"
     * Value: "12345"
     * Unit : "unit"
     * Consumption Value: "12345"
     * Consumption Unit: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non"
     * Expected result: Receiving an error:
     *                                      "You must enter a maximum of 25 characters"
     * @return void
     */
    public function test_add_pow_validated_addMenu_correctName_correctValue_correctUnit_correctConsumptionValue_tooLongConsumptionUnit() {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_name' => 'three',
            'pow_value' => '12345',
            'pow_unit' => 'unit',
            'pow_consumptionValue' => '12345',
            'pow_consumptionUnit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In non'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'pow_consumptionUnit' => 'You must enter a maximum of 25 characters'
        ]);
    }

    /**
     * Test Conception Number: 26
     * Saved a power as validated from add menu with a correct name, a correct value, a correct unit, a correct consumption value and a correct consumption unit and no type
     * Type: /
     * Name: "three"
     * Value: 220
     * Unit : "V"
     * Consumption Value: 18
     * Consumption Unit: "unit"
     * Expected result: Receiving an error: "You must choose a type for your power"
     * @returns void
     */
    public function test_add_pow_validated_addMenu_correctName_correctValue_correctUnit_correctConsumptionValue_correctConsumptionUnit_noType()
    {
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'unit'
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'pow_type' => 'You must choose a type for your power'
        ]);
    }

    /**
     * Test Conception Number 27
     * Saved successfully a power as validated from add menu
     * Type: Electric
     * Name: "three"
     * Value: 220
     * Unit: "V"
     * Consumption Value: 18
     * Consumption Unit: "kwH"
     * Expected result: The power is correctly saved in data base and correctly linked to the equipment
     * @returns void
     */
    public function test_add_pow_validated_addMenu_successfully_saved()
    {
        // Add the equipment
        $response=$this->post('equipment/verif', [
            'eq_internalReference' => 'validatedTest',
            'eq_externalReference' => 'validatedTest',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_internalReference' => 'validatedTest',
            'eq_externalReference' => 'validatedTest',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add the power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->last()->id
        ]);
    }

    /**
     * Test Conception Number: 28
     * Update successfully a power as drafted
     * Type: /
     * Name: "three"
     * Value: /
     * Unit: /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: The power is correctly updated in database
     * @returns void
     */
    public function test_update_pow_drafted_only_name()
    {
        // Add a new equipment
        $response=$this->post('equipment/verif', [
            'eq_internalReference' => 'updated1Test',
            'eq_externalReference' => 'updated1Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_internalReference' => 'updated1Test',
            'eq_externalReference' => 'updated1Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->last()->id
        ]);
        // Update the power type
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_name' => 'three'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/update/pow/'.Power::all()->last()->id, [
            'pow_validate' => 'drafted',
            'pow_name' => 'three',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'pow_name' => 'three',
            'equipmentTemp_id' => Equipment::all()->last()->id
        ]);
        $this->assertEquals($countPower, Power::all()->count());
    }

    /**
     * Test Conception Number: 29
     * Update successfully a power as drafted
     * Type: Example
     * Name: "Example"
     * Value: 138
     * Unit: 'w'
     * Consumption Value: 18
     * Consumption Unit: 'wh'
     * Expected result: The power is correctly updated in database
     * @returns void
     */
    public function test_update_pow_drafted_full()
    {
        // Add a new equipment
        $response=$this->post('equipment/verif', [
            'eq_internalReference' => 'updated2Test',
            'eq_externalReference' => 'updated2Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_internalReference' => 'updated2Test',
            'eq_externalReference' => 'updated2Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->last()->id
        ]);
        // Add a new power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Example'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Example'
        ]);
        // Update the power type
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Example',
            'pow_name' => 'Example',
            'pow_value' => 138,
            'pow_unit' => 'w',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'wH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/update/pow/'.Power::all()->last()->id, [
            'pow_validate' => 'drafted',
            'pow_type' => 'Example',
            'pow_name' => 'Example',
            'pow_value' => 138,
            'pow_unit' => 'w',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'wH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'pow_name' => 'Example',
            'pow_value' => 138,
            'pow_unit' => 'w',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'wH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->last()->id
        ]);
        $this->assertEquals($countPower, Power::all()->count());
    }

    /**
     * Test Conception Number: 30
     * Update successfully a power as to be validated
     * Type: /
     * Name: "three"
     * Value: /
     * Unit: /
     * Consumption Value: /
     * Consumption Unit: /
     * Expected result: The power is correctly updated in database
     * @returns void
     */
    public function test_update_pow_toBeValidated_only_name()
    {
        // Add a new equipment
        $response=$this->post('equipment/verif', [
            'eq_internalReference' => 'toBeVa1Test',
            'eq_externalReference' => 'toBeVa1Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_internalReference' => 'toBeVa1Test',
            'eq_externalReference' => 'toBeVa1Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'to_be_validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'to_be_validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'to_be_validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        // Update the power type
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_name' => 'three'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/update/pow/'.Power::all()->last()->id, [
            'pow_validate' => 'to_be_validated',
            'pow_name' => 'three',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'to_be_validated',
            'pow_name' => 'three',
            'equipmentTemp_id' => Equipment::all()->last()->id
        ]);
        $this->assertEquals($countPower, Power::all()->count());
    }

    /**
     * Test Conception Number: 31
     * Update successfully a power as to be validated
     * Type: Example
     * Name: "Example"
     * Value: 138
     * Unit: 'w'
     * Consumption Value: 18
     * Consumption Unit: 'wh'
     * Expected result: The power is correctly updated in database
     * @returns void
     */
    public function test_update_pow_toBeValidated_full()
    {
        // Add a new equipment
        $response=$this->post('equipment/verif', [
            'eq_internalReference' => 'toBeVa2Test',
            'eq_externalReference' => 'toBeVa2Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_internalReference' => 'toBeVa2Test',
            'eq_externalReference' => 'toBeVa2Test',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'to_be_validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'to_be_validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'to_be_validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        // Add a new power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Example'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Example'
        ]);
        // Update the power type
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'to_be_validated',
            'pow_type' => 'Example',
            'pow_name' => 'Example',
            'pow_value' => 138,
            'pow_unit' => 'w',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'wH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/update/pow/'.Power::all()->last()->id, [
            'pow_validate' => 'to_be_validated',
            'pow_type' => 'Example',
            'pow_name' => 'Example',
            'pow_value' => 138,
            'pow_unit' => 'w',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'wH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        // Verification on database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'to_be_validated',
            'pow_name' => 'Example',
            'pow_value' => 138,
            'pow_unit' => 'w',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'wH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->last()->id
        ]);
        $this->assertEquals($countPower, Power::all()->count());
    }

    /**
     * Test Conception Number: 32
     * Update successfully a power as validated
     * Type: Example
     * Name: "Example"
     * Value: 138
     * Unit: 'w'
     * Consumption Value: 18
     * Consumption Unit: 'wH'
     * Expected result: The power is correctly updated in database
     * @returns void
     */
    public function test_update_pow_validated_full()
    {
        // Add a new equipment
        $response=$this->post('equipment/verif', [
            'eq_internalReference' => 'validatedTest',
            'eq_externalReference' => 'validatedTest',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_internalReference' => 'validatedTest',
            'eq_externalReference' => 'validatedTest',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        // Add a new power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Example'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Example'
        ]);
        // Update the power type
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Example',
            'pow_name' => 'Example',
            'pow_value' => 138,
            'pow_unit' => 'w',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'wH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/update/pow/'.Power::all()->last()->id, [
            'pow_validate' => 'validated',
            'pow_type' => 'Example',
            'pow_name' => 'Example',
            'pow_value' => 138,
            'pow_unit' => 'w',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'wH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        // Verification on database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Example',
            'pow_value' => 138,
            'pow_unit' => 'w',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'wH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->last()->id
        ]);
        $this->assertEquals($countPower, Power::all()->count());
    }

    /**
     * Test Conception Number: 33
     * Update power type of validated equipment successfully
     * Type: Example
     * Name: "Electric source"
     * Value: 220
     * Unit: 'V'
     * Consumption Value: 29
     * Consumption Unit: 'kwH'
     * Expected result: The power type is correctly updated in the database,
     * the version number of the equipment has been incremented, the attributes qualityVerifier and TechnicalVerifier become NULL
     * and the attribute representing the creation of the life sheet takes the value false
     * @returns void
     */
    public function test_update_pow_type_of_validated_equipment()
    {
        // Add a mass unit
        $response=$this->post('/equipment/enum/massUnit/add', [
            'value' => 'g'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the mass unit doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_massUnit' => 'The value of the field for the new equipment mass unit already exist in the data base'
            ]);
        }
        // Add an equipment type
        $response=$this->post('/equipment/enum/type/add', [
            'value' => 'internal'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the equipment type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_type' => 'The value of the field for the new equipment type already exist in the data base'
            ]);
        }
        // Add a new equipment
        $response=$this->post('equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'upTypeTest',
            'eq_externalReference' => 'upTypeTest',
            'eq_name' => 'upTypeTest',
            'eq_serialNumber' => 'upTypeTest',
            'eq_constructor' => 'upTypeTest',
            'eq_set' => 'upTypeTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'upTypeTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'upTypeTest',
            'eq_externalReference' => 'upTypeTest',
            'eq_name' => 'upTypeTest',
            'eq_serialNumber' => 'upTypeTest',
            'eq_constructor' => 'upTypeTest',
            'eq_set' => 'upTypeTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'upTypeTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add a user
        $countUser=User::all()->count();
        $response=$this->post('register', [
            'user_firstName' => 'VerifierPow',
            'user_lastName' => 'VerifierPow',
            'user_pseudo' => 'VerifierPow',
            'user_password' => 'VerifierVerifier',
            'user_confirmation_password' => 'VerifierVerifier',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countUser+1, User::all()->count());
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        // Technical and quality verification
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        // Add a new power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Example'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Example'
        ]);
        $actualVersion = EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upTypeTest')->last()->eqTemp_version;
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upTypeTest')->last()->qualityVerifier_id);
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upTypeTest')->last()->technicalVerifier_id);
        // Update the power type
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Example',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/update/pow/'.Power::all()->last()->id, [
            'pow_validate' => 'validated',
            'pow_type' => 'Example',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        // Verification on database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->last()->id
        ]);
        $this->assertEquals($countPower, Power::all()->count());
        $this->assertEquals($actualVersion+1, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upTypeTest')->last()->eqTemp_version);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upTypeTest')->last()->qualityVerifier_id);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upTypeTest')->last()->technicalVerifier_id);
        $this->assertEquals(0, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upTypeTest')->last()->eqTemp_lifeSheetCreated);
    }

    /**
     * Test Conception Number: 34
     * Update power name of validated equipment successfully
     * Type: Electric
     * Name: "Example"
     * Value: 220
     * Unit: 'V'
     * Consumption Value: 29
     * Consumption Unit: 'kwH'
     * Expected result: The power type is correctly updated in the database,
     * the version number of the equipment has been incremented, the attributes qualityVerifier and TechnicalVerifier become NULL
     * and the attribute representing the creation of the life sheet takes the value false
     * @returns void
     */
    public function test_update_pow_name_of_validated_equipment()
    {
        // Add a mass unit
        $response=$this->post('/equipment/enum/massUnit/add', [
            'value' => 'g'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the mass unit doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_massUnit' => 'The value of the field for the new equipment mass unit already exist in the data base'
            ]);
        }
        // Add an equipment type
        $response=$this->post('/equipment/enum/type/add', [
            'value' => 'internal'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the equipment type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_type' => 'The value of the field for the new equipment type already exist in the data base'
            ]);
        }
        // Add a new equipment
        $response=$this->post('equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'upNameTest',
            'eq_externalReference' => 'upNameTest',
            'eq_name' => 'upNameTest',
            'eq_serialNumber' => 'upNameTest',
            'eq_constructor' => 'upNameTest',
            'eq_set' => 'upNameTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'upNameTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'upNameTest',
            'eq_externalReference' => 'upNameTest',
            'eq_name' => 'upNameTest',
            'eq_serialNumber' => 'upNameTest',
            'eq_constructor' => 'upNameTest',
            'eq_set' => 'upNameTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'upNameTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add a user
        $countUser=User::all()->count();
        $response=$this->post('register', [
            'user_firstName' => 'VerifierPow',
            'user_lastName' => 'VerifierPow',
            'user_pseudo' => 'VerifierPow',
            'user_password' => 'VerifierVerifier',
            'user_confirmation_password' => 'VerifierVerifier',
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
            $this->assertEquals($countUser+1, User::all()->count());
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'user_pseudo' => 'This username is already used'
            ]);
        }

        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        // Technical and quality verification
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $actualVersion = EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upNameTest')->last()->eqTemp_version;
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upNameTest')->last()->qualityVerifier_id);
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upNameTest')->last()->technicalVerifier_id);
        // Update the power type
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Example',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/update/pow/'.Power::all()->last()->id, [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Example',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        // Verification on database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Example',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        $this->assertEquals($countPower, Power::all()->count());
        $this->assertEquals($actualVersion+1, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upNameTest')->last()->eqTemp_version);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upNameTest')->last()->qualityVerifier_id);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upNameTest')->last()->technicalVerifier_id);
        $this->assertEquals(0, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upNameTest')->last()->eqTemp_lifeSheetCreated);
    }

    /**
     * Test Conception Number: 35
     * Update power value of validated equipment successfully
     * Type: Electric
     * Name: "Electric source"
     * Value: 138
     * Unit: 'V'
     * Consumption Value: 29
     * Consumption Unit: 'kwH'
     * Expected result: The power type is correctly updated in the database,
     * the version number of the equipment has been incremented, the attributes qualityVerifier and TechnicalVerifier become NULL
     * and the attribute representing the creation of the life sheet takes the value false
     * @returns void
     */
    public function test_update_pow_value_of_validated_equipment()
    {
        // Add a mass unit
        $response=$this->post('/equipment/enum/massUnit/add', [
            'value' => 'g'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the mass unit doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_massUnit' => 'The value of the field for the new equipment mass unit already exist in the data base'
            ]);
        }
        // Add an equipment type
        $response=$this->post('/equipment/enum/type/add', [
            'value' => 'internal'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the equipment type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_type' => 'The value of the field for the new equipment type already exist in the data base'
            ]);
        }
        // Add a new equipment
        $response=$this->post('equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'upValTest',
            'eq_externalReference' => 'upValTest',
            'eq_name' => 'upValTest',
            'eq_serialNumber' => 'upValTest',
            'eq_constructor' => 'upValTest',
            'eq_set' => 'upValTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'upValTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'upValTest',
            'eq_externalReference' => 'upValTest',
            'eq_name' => 'upValTest',
            'eq_serialNumber' => 'upValTest',
            'eq_constructor' => 'upValTest',
            'eq_set' => 'upValTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'upValTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add a user
        $countUser=User::all()->count();
        $response=$this->post('register', [
            'user_firstName' => 'VerifierPow',
            'user_lastName' => 'VerifierPow',
            'user_pseudo' => 'VerifierPow',
            'user_password' => 'VerifierVerifier',
            'user_confirmation_password' => 'VerifierVerifier',
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
            $this->assertEquals($countUser+1, User::all()->count());
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'user_pseudo' => 'This username is already used'
            ]);
        }
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        // Technical and quality verification
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $actualVersion = EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upValTest')->last()->eqTemp_version;
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upValTest')->last()->qualityVerifier_id);
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upValTest')->last()->technicalVerifier_id);
        // Update the power type
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 138,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/update/pow/'.Power::all()->last()->id, [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 138,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        // Verification on database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 138,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        $this->assertEquals($countPower, Power::all()->count());
        $this->assertEquals($actualVersion+1, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upValTest')->last()->eqTemp_version);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upValTest')->last()->qualityVerifier_id);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upValTest')->last()->technicalVerifier_id);
        $this->assertEquals(0, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upValTest')->last()->eqTemp_lifeSheetCreated);
    }

    /**
     * Test Conception Number: 36
     * Update power unit of validated equipment successfully
     * Type: Electric
     * Name: "Electric source"
     * Value: 18
     * Unit: 'w'
     * Consumption Value: 29
     * Consumption Unit: 'kwH'
     * Expected result: The power type is correctly updated in the database,
     * the version number of the equipment has been incremented, the attributes qualityVerifier and TechnicalVerifier become NULL
     * and the attribute representing the creation of the life sheet takes the value false
     * @returns void
     */
    public function test_update_pow_unit_of_validated_equipment()
    {
        // Add a mass unit
        $response=$this->post('/equipment/enum/massUnit/add', [
            'value' => 'g'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the mass unit doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_massUnit' => 'The value of the field for the new equipment mass unit already exist in the data base'
            ]);
        }
        // Add an equipment type
        $response=$this->post('/equipment/enum/type/add', [
            'value' => 'internal'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the equipment type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_type' => 'The value of the field for the new equipment type already exist in the data base'
            ]);
        }
        // Add a new equipment
        $response=$this->post('equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'upUnitTest',
            'eq_externalReference' => 'upUnitTest',
            'eq_name' => 'upUnitTest',
            'eq_serialNumber' => 'upUnitTest',
            'eq_constructor' => 'upUnitTest',
            'eq_set' => 'upUnitTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'upUnitTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'upUnitTest',
            'eq_externalReference' => 'upUnitTest',
            'eq_name' => 'upUnitTest',
            'eq_serialNumber' => 'upUnitTest',
            'eq_constructor' => 'upUnitTest',
            'eq_set' => 'upUnitTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'upUnitTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add a user
        $countUser=User::all()->count();
        $response=$this->post('register', [
            'user_firstName' => 'VerifierPow',
            'user_lastName' => 'VerifierPow',
            'user_pseudo' => 'VerifierPow',
            'user_password' => 'VerifierVerifier',
            'user_confirmation_password' => 'VerifierVerifier',
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
            $this->assertEquals($countUser+1, User::all()->count());
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'user_pseudo' => 'This username is already used'
            ]);
        }
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        // Technical and quality verification
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $actualVersion = EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upUnitTest')->last()->eqTemp_version;
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upUnitTest')->last()->qualityVerifier_id);
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upUnitTest')->last()->technicalVerifier_id);
        // Update the power type
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 18,
            'pow_unit' => 'w',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/update/pow/'.Power::all()->last()->id, [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 18,
            'pow_unit' => 'w',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        // Verification on database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 18,
            'pow_unit' => 'w',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        $this->assertEquals($countPower, Power::all()->count());
        $this->assertEquals($actualVersion+1, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upUnitTest')->last()->eqTemp_version);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upUnitTest')->last()->qualityVerifier_id);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upUnitTest')->last()->technicalVerifier_id);
        $this->assertEquals(0, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upUnitTest')->last()->eqTemp_lifeSheetCreated);
    }

    /**
     * Test Conception Number: 37
     * Update power consumption value of validated equipment successfully
     * Type: Electric
     * Name: "Electric source"
     * Value: 18
     * Unit: 'V'
     * Consumption Value: 18
     * Consumption Unit: 'kwH'
     * Expected result: The power type is correctly updated in the database,
     * the version number of the equipment has been incremented, the attributes qualityVerifier and TechnicalVerifier become NULL
     * and the attribute representing the creation of the life sheet takes the value false
     * @returns void
     */
    public function test_update_pow_consumption_value_of_validated_equipment()
    {
        // Add a mass unit
        $response=$this->post('/equipment/enum/massUnit/add', [
            'value' => 'g'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the mass unit doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_massUnit' => 'The value of the field for the new equipment mass unit already exist in the data base'
            ]);
        }
        // Add an equipment type
        $response=$this->post('/equipment/enum/type/add', [
            'value' => 'internal'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the equipment type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_type' => 'The value of the field for the new equipment type already exist in the data base'
            ]);
        }
        // Add a new equipment
        $response=$this->post('equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'upCValTest',
            'eq_externalReference' => 'upCValTest',
            'eq_name' => 'upCValTest',
            'eq_serialNumber' => 'upCValTest',
            'eq_constructor' => 'upCValTest',
            'eq_set' => 'upCValTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'upCValTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'upCValTest',
            'eq_externalReference' => 'upCValTest',
            'eq_name' => 'upCValTest',
            'eq_serialNumber' => 'upCValTest',
            'eq_constructor' => 'upCValTest',
            'eq_set' => 'upCValTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'upCValTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add a user
        $countUser=User::all()->count();
        $response=$this->post('register', [
            'user_firstName' => 'VerifierPow',
            'user_lastName' => 'VerifierPow',
            'user_pseudo' => 'VerifierPow',
            'user_password' => 'VerifierVerifier',
            'user_confirmation_password' => 'VerifierVerifier',
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
            $this->assertEquals($countUser+1, User::all()->count());
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'user_pseudo' => 'This username is already used'
            ]);
        }
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        // Technical and quality verification
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $actualVersion = EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upCValTest')->last()->eqTemp_version;
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upCValTest')->last()->qualityVerifier_id);
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upCValTest')->last()->technicalVerifier_id);
        // Update the power type
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 18,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/update/pow/'.Power::all()->last()->id, [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 18,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        // Verification on database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 18,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        $this->assertEquals($countPower, Power::all()->count());
        $this->assertEquals($actualVersion+1, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upCValTest')->last()->eqTemp_version);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upCValTest')->last()->qualityVerifier_id);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upCValTest')->last()->technicalVerifier_id);
        $this->assertEquals(0, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upCValTest')->last()->eqTemp_lifeSheetCreated);
    }

    /**
     * Test Conception Number: 38
     * Update power consumption unit of validated equipment successfully
     * Type: Electric
     * Name: "Electric source"
     * Value: 18
     * Unit: 'V'
     * Consumption Value: 18
     * Consumption Unit: 'wH'
     * Expected result: The power type is correctly updated in the database,
     * the version number of the equipment has been incremented, the attributes qualityVerifier and TechnicalVerifier become NULL
     * and the attribute representing the creation of the life sheet takes the value false
     * @returns void
     */
    public function test_update_pow_consumption_unit_of_validated_equipment()
    {
        // Add a mass unit
        $response=$this->post('/equipment/enum/massUnit/add', [
            'value' => 'g'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the mass unit doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_massUnit' => 'The value of the field for the new equipment mass unit already exist in the data base'
            ]);
        }
        // Add an equipment type
        $response=$this->post('/equipment/enum/type/add', [
            'value' => 'internal'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the equipment type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_type' => 'The value of the field for the new equipment type already exist in the data base'
            ]);
        }
        // Add a new equipment
        $response=$this->post('equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'upCUnitTest',
            'eq_externalReference' => 'upCUnitTest',
            'eq_name' => 'upCUnitTest',
            'eq_serialNumber' => 'upCUnitTest',
            'eq_constructor' => 'upCUnitTest',
            'eq_set' => 'upCUnitTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'upCUnitTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'upCUnitTest',
            'eq_externalReference' => 'upCUnitTest',
            'eq_name' => 'upCUnitTest',
            'eq_serialNumber' => 'upCUnitTest',
            'eq_constructor' => 'upCUnitTest',
            'eq_set' => 'upCUnitTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'upCUnitTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add a user
        $countUser=User::all()->count();
        $response=$this->post('register', [
            'user_firstName' => 'VerifierPow',
            'user_lastName' => 'VerifierPow',
            'user_pseudo' => 'VerifierPow',
            'user_password' => 'VerifierVerifier',
            'user_confirmation_password' => 'VerifierVerifier',
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
            $this->assertEquals($countUser+1, User::all()->count());
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'user_pseudo' => 'This username is already used'
            ]);
        }
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        // Technical and quality verification
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $actualVersion = EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upCUnitTest')->last()->eqTemp_version;
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upCUnitTest')->last()->qualityVerifier_id);
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upCUnitTest')->last()->technicalVerifier_id);
        // Update the power type
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 18,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'wH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/update/pow/'.Power::all()->last()->id, [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 18,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'wH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        // Verification on database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 18,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'wH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        $this->assertEquals($countPower, Power::all()->count());
        $this->assertEquals($actualVersion+1, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upCUnitTest')->last()->eqTemp_version);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upCUnitTest')->last()->qualityVerifier_id);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upCUnitTest')->last()->technicalVerifier_id);
        $this->assertEquals(0, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'upCUnitTest')->last()->eqTemp_lifeSheetCreated);
    }

    /**
     * Test Conception Number: 39
     * Add successfully a validated power to a validated equipment
     * @return void
     */
    public function test_add_validated_power_to_validated_equipment()
    {
        // Add a mass unit
        $response=$this->post('/equipment/enum/massUnit/add', [
            'value' => 'g'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the mass unit doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_massUnit' => 'The value of the field for the new equipment mass unit already exist in the data base'
            ]);
        }
        // Add an equipment type
        $response=$this->post('/equipment/enum/type/add', [
            'value' => 'internal'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the equipment type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_type' => 'The value of the field for the new equipment type already exist in the data base'
            ]);
        }
        // Add a new equipment
        $response=$this->post('equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'addValidTest',
            'eq_externalReference' => 'addValidTest',
            'eq_name' => 'addValidTest',
            'eq_serialNumber' => 'addValidTest',
            'eq_constructor' => 'addValidTest',
            'eq_set' => 'addValidTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'addValidTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'addValidTest',
            'eq_externalReference' => 'addValidTest',
            'eq_name' => 'addValidTest',
            'eq_serialNumber' => 'addValidTest',
            'eq_constructor' => 'addValidTest',
            'eq_set' => 'addValidTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'addValidTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add a user
        $countUser=User::all()->count();
        $response=$this->post('register', [
            'user_firstName' => 'VerifierPow',
            'user_lastName' => 'VerifierPow',
            'user_pseudo' => 'VerifierPow',
            'user_password' => 'VerifierVerifier',
            'user_confirmation_password' => 'VerifierVerifier',
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
            $this->assertEquals($countUser+1, User::all()->count());
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'user_pseudo' => 'This username is already used'
            ]);
        }
        // Technical and quality verification
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $actualVersion = EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addValidTest')->last()->eqTemp_version;
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addValidTest')->last()->qualityVerifier_id);
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addValidTest')->last()->technicalVerifier_id);
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        $this->assertEquals($actualVersion+1, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addValidTest')->last()->eqTemp_version);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addValidTest')->last()->qualityVerifier_id);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addValidTest')->last()->technicalVerifier_id);
        $this->assertEquals(0, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'addValidTest')->last()->eqTemp_lifeSheetCreated);
    }

    /**
     * Test Conception Number: 40
     * Consult the power of an equipment
     * @return void
     */
    public function test_consult_power_in_equipment()
    {
        // Add a new equipment
        $countEquipment = Equipment::all()->count();
        $response = $this->post('equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'consult1Test',
            'eq_externalReference' => 'consult1Test'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'consult1Test',
            'eq_externalReference' => 'consult1Test'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power to this equipment
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        // Consult the power of this equipment
        $response = $this->get('/power/send/'.Equipment::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'id' => Power::all()->last()->id,
                'pow_name' => 'Electric source',
                'pow_value' => 220,
                'pow_unit' => 'V',
                'pow_consumptionValue' => 29,
                'pow_consumptionUnit' => 'kwH',
                'pow_validate' => 'validated',
                'pow_type' => 'Electric'
            ]]);
    }

    /**
     * Test Conception Number: 41
     * Consult the power of an equipment by type
     * @return void
     */
    public function test_consult_power_in_equipment_by_type()
    {
        // Add a new equipment
        $countEquipment = Equipment::all()->count();
        $response = $this->post('equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'consult2Test',
            'eq_externalReference' => 'consult2Test'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'consult2Test',
            'eq_externalReference' => 'consult2Test'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Example'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Example'
        ]);
        // Add a power to this equipment
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        // Consult the power of this equipment
        $response = $this->get('/power/send/ByType/'.Equipment::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'type' => 'Electric',
                'powers' => [[
                    'id' => Power::all()->last()->id,
                    'pow_name' => 'Electric source',
                    'pow_value' => 220,
                    'pow_unit' => 'V',
                    'pow_consumptionValue' => 29,
                    'pow_consumptionUnit' => 'kwH',
                    'pow_validate' => 'validated',
                    'pow_type' => 'Electric'
                ]]
            ],
            '1' => [
                'type' => 'Example',
                'powers' => []
            ]
        ]);
    }

    /**
     * Test Conception Number: 42
     * Consult the names of the power
     * @return void
     */
    public function test_consult_power_names()
    {
        // Add a new equipment
        $countEquipment = Equipment::all()->count();
        $response = $this->post('equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'consult3Test',
            'eq_externalReference' => 'consult3Test'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'consult3Test',
            'eq_externalReference' => 'consult3Test'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Example'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Example'
        ]);
        // Add a power to this equipment
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Example',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Example',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Example',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        // Consult the power of this equipment
        $response = $this->get('/power/names');
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                'pow_name' => 'three'
            ],
            '1' => [
                'pow_name' => 'Example'
            ],
            '2' => [
                'pow_name' => 'Electric source'
            ]
        ]);
    }

    /**
     * Test Conception Number: 43
     * Delete the power previously created
     * @return void
     */
    public function test_delete_power()
    {
        // Add a new equipment
        $countEquipment = Equipment::all()->count();
        $response = $this->post('equipment/verif', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'removeTest',
            'eq_externalReference' => 'removeTest'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response = $this->post('/equipment/add', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'removeTest',
            'eq_externalReference' => 'removeTest'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power to this equipment
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        // Delete the power
        $response = $this->post('/equipment/delete/pow/'.Power::all()->last()->id, [
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower, Power::all()->count());
    }

    /**
     * Test Conception Number: 44
     * Delete the power previously created of an validated equipment
     * @return void
     */
    public function test_delete_power_from_validated_equipment()
    {
        // Add a mass unit
        $response=$this->post('/equipment/enum/massUnit/add', [
            'value' => 'g'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the mass unit doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_massUnit' => 'The value of the field for the new equipment mass unit already exist in the data base'
            ]);
        }
        // Add an equipment type
        $response=$this->post('/equipment/enum/type/add', [
            'value' => 'internal'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the equipment type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_eq_type' => 'The value of the field for the new equipment type already exist in the data base'
            ]);
        }
        // Add a new equipment
        $response=$this->post('equipment/verif', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'removePowerTest',
            'eq_externalReference' => 'removePowerTest',
            'eq_name' => 'removePowerTest',
            'eq_serialNumber' => 'removePowerTest',
            'eq_constructor' => 'removePowerTest',
            'eq_set' => 'removePowerTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'removePowerTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $countEquipment = Equipment::all()->count();
        $response=$this->post('equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'removePowerTest',
            'eq_externalReference' => 'removePowerTest',
            'eq_name' => 'removePowerTest',
            'eq_serialNumber' => 'removePowerTest',
            'eq_constructor' => 'removePowerTest',
            'eq_set' => 'removePowerTest',
            'eq_massUnit' => 'g',
            'eq_mass' => 10,
            'eq_remarks' => 'removePowerTest',
            'eq_mobility' => true,
            'eq_type' => 'internal'
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        // Add a user
        $countUser=User::all()->count();
        $response=$this->post('register', [
            'user_firstName' => 'VerifierPow',
            'user_lastName' => 'VerifierPow',
            'user_pseudo' => 'VerifierPow',
            'user_password' => 'VerifierVerifier',
            'user_confirmation_password' => 'VerifierVerifier',
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
            $this->assertEquals($countUser+1, User::all()->count());
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'user_pseudo' => 'This username is already used'
            ]);
        }
        // Add the power type
        $response=$this->post('/power/enum/type/add', [
            'value' => 'Electric'
        ]);
        if ($response->getStatusCode() === 200) {
            // If the power type doesn't already exist in the database
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429);
            $response->assertInvalid([
                'enum_pow_type' => 'The value of the field for the new power type already exist in the data base'
            ]);
        }
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Electric'
        ]);
        // Add a power
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH'
        ]);
        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'validated',
            'pow_type' => 'Electric',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower+1, Power::all()->count());
        // Verification in the database
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'validated',
            'pow_name' => 'Electric source',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 29,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'enumPowerType_id' => EnumPowerType::all()->where('value', '==', 'Electric')->first()->id
        ]);
        // Technical and quality verification
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $actualVersion = EquipmentTemp::all()->where('eqTemp_remarks', '==', 'removePowerTest')->last()->eqTemp_version;
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'removePowerTest')->last()->qualityVerifier_id);
        $this->assertEquals(User::all()->where('user_pseudo', '==', 'VerifierPow')->first()->id, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'removePowerTest')->last()->technicalVerifier_id);
        // Delete the power
        $response=$this->post('/equipment/delete/pow/'.Power::all()->last()->id, [
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countPower, Power::all()->count());
        // Check the status of the equipment
        $this->assertEquals($actualVersion+1, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'removePowerTest')->last()->eqTemp_version);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'removePowerTest')->last()->technicalVerifier_id);
        $this->assertNull(EquipmentTemp::all()->where('eqTemp_remarks', '==', 'removePowerTest')->last()->qualityVerifier_id);
        $this->assertEquals(0, EquipmentTemp::all()->where('eqTemp_remarks', '==', 'removePowerTest')->last()->eqTemp_lifeSheetCreated);
    }
}

?>
