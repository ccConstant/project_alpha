<?php

/*
* Filename : DimensionTest.php
* Creation date : 31 May 2022
* Update date : 22 Mar 2023
* This file contains all the tests about the dimension table.
* Coverage : 100%
*/

namespace Tests\Feature;

use App\Models\SW01\Dimension;
use App\Models\SW01\EnumDimensionName;
use App\Models\SW01\EnumDimensionType;
use App\Models\SW01\EnumDimensionUnit;
use App\Models\SW01\EnumEquipmentMassUnit;
use App\Models\SW01\EnumEquipmentType;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class DimensionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Conception Number : 1
     * Saved a dimension as drafted from add menu with no value
     * Type : /  Name : /  Value : / Unit : /
     * Expected result : Receiving an error "You must enter a value for your dimension"
     * @return void
     */
    public function test_add_dim_drafted_addMenu_NoValue()
    {
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'drafted'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'dim_value' => 'You must enter a value for your dimension'
        ]);
    }

    /**
     * Test Conception Number : 2
     * Saved a dimension as drafted from add menu with a too long value
     * Type : /  Name : /  Value : "123456789123456789123456789123456789123456789123456789" Unit : /
     * Expected result : Receiving an error "You must enter a maximum of 50 characters"
     * @return void
     */
    public function test_add_dim_drafted_addMenu_TooLongValue()
    {

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'drafted',
            'dim_value' => '123456789123456789123456789123456789123456789123456789'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'dim_value' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number : 3
     * Saved successfully a dimension as drafted from add menu
     * Type : /  Name : /  Value : 47 Unit : /
     * Expected result : The dimension is correctly saved in data base and correctly linked to the equipment
     * @return void
     */
    public function test_add_dim_drafted_addMenu_CorrectValue()
    {
        $this->create_equipment_and_user('Test', 'drafted');
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'drafted',
            'dim_value' => '47'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_validate' => 'drafted',
            'dim_value' => '47',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => NULL,
            'enumDimensionName_id' => NULL,
            'dim_value' => 47,
            'enumDimensionUnit_id' => NULL,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
    }

    public function create_equipment_and_user($name, $validate, $verifier = null)
    {
        // ->where('eq_validate', '=', $validate)
        if (Equipment::all()->where('eq_internalReference', '=', $name)->count() === 0) {
            $countEq = Equipment::all()->count();
            $response = $this->post('/equipment/verif', [
                'eq_internalReference' => $name,
                'eq_externalReference' => $name,
                'reason' => 'add',
                'eq_validate' => $validate
            ]);
            $response->assertStatus(200);
            $response = $this->post('/equipment/add', [
                'eq_internalReference' => $name,
                'eq_externalReference' => $name,
                'reason' => 'add',
                'eq_validate' => $validate

            ]);
            $response->assertStatus(200);
            $this->assertCount($countEq + 1, Equipment::all());

            if (User::all()->where('user_pseudo', '=', $verifier)->count() === 0) {
                $countUser = User::all()->count();
                $response = $this->post('register', [
                    'user_firstName' => $verifier,
                    'user_lastName' => $verifier,
                    'user_pseudo' => $verifier,
                    'user_password' => $verifier . $verifier . $verifier . $verifier,
                    'user_confirmation_password' => $verifier . $verifier . $verifier . $verifier,
                ]);
                $response->assertStatus(200);
                $this->assertCount($countUser + 1, User::all());
            }

        }
    }

    /**
     * Test Conception Number : 4
     * Saved successfully a dimension as drafted from add menu
     * Type : External  Name : Length  Value : 18 Unit : mm
     * Expected result : The dimension is correctly saved in data base and correctly linked to the equipment
     * @return void
     */
    public function test_add_dim_drafted_addMenu_CorrectValues()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => 1
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => 1
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => 1
        ]);
    }

    public function create_required_enum()
    {
        if (EnumDimensionType::all()->where('value', '=', 'External')->count() === 0) {
            $countDimType = EnumDimensionType::all()->count();
            $response = $this->post('/dimension/enum/type/add', [
                'value' => 'External',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimType + 1, EnumDimensionType::all());

        }
        if (EnumDimensionType::all()->where('value', '=', 'Internal')->count() === 0) {
            $countDimType = EnumDimensionType::all()->count();
            $response = $this->post('/dimension/enum/type/add', [
                'value' => 'Internal',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimType + 1, EnumDimensionType::all());

        }
        if (EnumDimensionName::all()->where('value', '=', 'Length')->count() === 0) {
            $countDimName = EnumDimensionName::all()->count();
            $response = $this->post('/dimension/enum/name/add', [
                'value' => 'Length',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimName + 1, EnumDimensionName::all());
        }
        if (EnumDimensionName::all()->where('value', '=', 'Width')->count() === 0) {
            $countDimName = EnumDimensionName::all()->count();
            $response = $this->post('/dimension/enum/name/add', [
                'value' => 'Width',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimName + 1, EnumDimensionName::all());
        }
        if (EnumDimensionUnit::all()->where('value', '=', 'mm')->count() === 0) {
            $countDimUnit = EnumDimensionUnit::all()->count();
            $response = $this->post('/dimension/enum/unit/add', [
                'value' => 'mm',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimUnit + 1, EnumDimensionUnit::all());
        }
        if (EnumDimensionUnit::all()->where('value', '=', 'cm')->count() === 0) {
            $countDimUnit = EnumDimensionUnit::all()->count();
            $response = $this->post('/dimension/enum/unit/add', [
                'value' => 'cm',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimUnit + 1, EnumDimensionUnit::all());
        }

        if (EnumEquipmentType::all()->where('value', '=', 'Internal')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'Internal',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());

        }
        if (EnumEquipmentType::all()->where('value', '=', 'External')->count() === 0) {
            $countEqType = EnumEquipmentType::all()->count();
            $response = $this->post('/equipment/enum/type/add', [
                'value' => 'External',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType + 1, EnumEquipmentType::all());

        }
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'kg')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'kg',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'g')->count() === 0) {
            $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
            $response = $this->post('/equipment/enum/massUnit/add', [
                'value' => 'g',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());
        }
    }

    /**
     * Test Conception Number : 5
     * Saved a dimension as to be validated from add menu with no value
     * Type : /  Name : /  Value : / Unit : /
     * Expected result : Receiving an error "You must enter a value for your dimension"
     * @return void
     */
    public function test_add_dim_toBeValidated_addMenu_NoValue()
    {

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'to_be_validated'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'dim_value' => 'You must enter a value for your dimension'
        ]);
    }

    /**
     * Test Conception Number : 6
     * Saved a dimension as to be validated from add menu with a too long value
     * Type : /  Name : /  Value : "123456789123456789123456789123456789123456789123456789" Unit : /
     * Expected result : Receiving an error "You must enter a maximum of 50 characters"
     * @return void
     */
    public function test_add_dim_toBeValidated_addMenu_TooLongValue()
    {
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'to_be_validated',
            'dim_value' => '123456789123456789123456789123456789123456789123456789'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'dim_value' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number : 7
     * Saved successfully a dimension as to be validated from add menu
     * Type : /  Name : /  Value : 47 Unit : /
     * Expected result : The dimension is correctly saved in data base and correctly linked to the equipment
     * @return void
     */
    public function test_add_dim_toBeValidated_addMenu_CorrectValue()
    {
        $this->create_equipment_and_user('Test', 'drafted');
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'to_be_validated',
            'dim_value' => '8930'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_validate' => 'to_be_validated',
            'dim_value' => '8930',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => NULL,
            'enumDimensionName_id' => NULL,
            'dim_value' => 8930,
            'dim_validate' => 'to_be_validated',
            'enumDimensionUnit_id' => NULL,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id
        ]);
    }


    /**
     * Test Conception Number : 8
     * Saved successfully a dimension as to be validated from add menu
     * Type : External  Name : Length  Value : 18 Unit : mm
     * Expected result : The dimension is correctly saved in data base and correctly linked to the equipment
     * @return void
     */
    public function test_add_dim_toBeValidated_addMenu_CorrectValues()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'to_be_validated',
            'dim_value' => '18',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'to_be_validated',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'to_be_validated',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id
        ]);
    }

    /**
     * Test Conception Number : 9
     * Saved a dimension as validated from add menu with no value
     * Type : /  Name : /  Value : / Unit : /
     * Expected result : Receiving an error "You must enter a value for your dimension"
     * @return void
     */
    public function test_add_dim_validated_addMenu_NoValue()
    {
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'validated'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'dim_value' => 'You must enter a value for your dimension'
        ]);
        $this->assertCount($countDim, Dimension::all());
    }

    /**
     * Test Conception Number : 10
     * Saved a dimension as validated from add menu with a too long value
     * Type : /  Name : /  Value : "123456789123456789123456789123456789123456789123456789" Unit : /
     * Expected result : Receiving an error "You must enter a maximum of 50 characters"
     * @return void
     */
    public function test_add_dim_validated_addMenu_TooLongValue()
    {
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value' => '123456789123456789123456789123456789123456789123456789'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'dim_value' => 'You must enter a maximum of 50 characters'
        ]);
        $this->assertCount($countDim, Dimension::all());
    }

    /**
     * Test Conception Number : 11
     * Saved a dimension as validated  from add menu with a correct value but no type, no name and no unit
     * Type : /  Name : /  Value : 32 Unit : /
     * Expected result : Receiving errors "You must choose a type for your dimension", "You must choose a name for your dimension", "You must choose a unit for your dimension"
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValueOnly()
    {
        $this->create_equipment_and_user('Test', 'drafted');
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value' => '32'
        ]);

        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_unit' => 'You must choose a unit for your dimension',
            'dim_type' => 'You must choose a type for your dimension',
            'dim_name' => 'You must choose a name for your dimension',
        ]);
        $this->assertCount($countDim, Dimension::all());
    }

    /**
     * Test Conception Number : 12
     * Saved a dimension as validated  from add menu with a correct value but no type and no name
     * Type : /  Name : /  Value : 18 Unit : mm
     * Expected result : Receiving errors "You must choose a type for your dimension", "You must choose a name for your dimension"
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValueUnit()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_unit' => 'mm'
        ]);

        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_type' => 'You must choose a type for your dimension',
            'dim_name' => 'You must choose a name for your dimension',
        ]);
        $this->assertCount($countDim, Dimension::all());
    }

    /**
     * Test Conception Number : 13
     * Saved a dimension as validated  from add menu with a correct value but no type and no unit
     * Type : /  Name : Length  Value : 18 Unit : /
     * Expected result : Receiving errors "You must choose a type for your dimension","You must choose a unit for your dimension"
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValueName()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_name' => 'Length'
        ]);

        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_type' => 'You must choose a type for your dimension',
            'dim_unit' => 'You must choose a unit for your dimension',
        ]);
        $this->assertCount($countDim, Dimension::all());
    }

    /**
     * Test Conception Number : 14
     * Saved a dimension as validated  from add menu with a correct value but no type
     * Type : /  Name : Length  Value : 18 Unit : mm
     * Expected result : Receiving errors "You must choose a type for your dimension",
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValueNameUnit()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_name' => 'Length',
            'dim_unit' => 'mm'
        ]);

        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_type' => 'You must choose a type for your dimension',
        ]);
        $this->assertCount($countDim, Dimension::all());
    }

    /**
     * Test Conception Number : 15
     * Saved a dimension as validated  from add menu with a correct value but no name and no unit
     * Type : External  Name : /  Value : 18 Unit : /
     * Expected result : Receiving errors "You must choose a name for your dimension", "You must choose a unit for your dimension"
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValueType()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_type' => 'External',
        ]);

        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_name' => 'You must choose a name for your dimension',
            'dim_unit' => 'You must choose a unit for your dimension',
        ]);
        $this->assertCount($countDim, Dimension::all());
    }

    /**
     * Test Conception Number : 16
     * Saved a dimension as validated  from add menu with a correct value but no name
     * Type : External  Name : /  Value : 18 Unit : mm
     * Expected result : Receiving errors "You must choose a name for your dimension"
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValueTypeUnit()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_type' => 'External',
            'dim_unit' => 'mm'
        ]);

        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_name' => 'You must choose a name for your dimension',
        ]);
        $this->assertCount($countDim, Dimension::all());
    }

    /**
     * Test Conception Number : 17
     * Saved a dimension as validated  from add menu with a correct value but no unit
     * Type : External  Name : Length  Value : 18 Unit : /
     * Expected result : Receiving errors "You must choose a unit for your dimension"
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValueTypeName()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_type' => 'External',
            'dim_name' => 'Length'
        ]);

        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_unit' => 'You must choose a unit for your dimension',
        ]);
        $this->assertCount($countDim, Dimension::all());
    }

    /**
     * Test Conception Number : 18
     * Saved successfully a dimension as validated from add menu
     * Type : External  Name : Length  Value : 18 Unit : mm
     * Expected result : The dimension is correctly saved in data base and correctly linked to the equipment
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValues()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id
        ]);
    }


    /**
     * Test Conception Number : 21
     * Update successfully a dimension as drafted with only a value
     * Type : /  Name : /  Value : 47 Unit : /
     * Expected result : The dimension is correctly updated in data base
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests
     * Type : External  Name : Length  Value : 29 Unit : mm
     * Eq External Ref / Eq Internal Ref : Example1
     * Eq set : /
     * @return void
     */

    public function test_update_dim_draft_correctValue()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value' => '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value' => '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'drafted',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id
        ]);
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value' => '47',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $url = '/equipment/update/dim/' . Dimension::all()->last()->id;
        $response = $this->post($url, [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value' => '47',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id,
            'dim_value' => 47,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'drafted',
        ]);

    }

    /**
     * Test Conception Number : 22
     * Update successfully a dimension as drafted with a type, name value and a unit
     * Type : Internal  Name : Width  Value : 18 Unit : cm
     * Expected result : The dimension is correctly updated in data base
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests
     * Type : External  Name : Length  Value : 29 Unit : mm
     * Eq External Ref / Eq Internal Ref : Example1
     * Eq set : /
     * @return void
     */

    public function test_update_dim_drafted_correctValues()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value' => '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value' => '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'drafted',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'Internal',
            'id' => EnumDimensionType::all()->where('value', '=', 'Internal')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Width',
            'id' => EnumDimensionName::all()->where('value', '=', 'Width')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'cm',
            'id' => EnumDimensionUnit::all()->where('value', '=', 'cm')->first()->id
        ]);
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'Internal',
            'dim_name' => 'Width',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'cm'
        ]);
        $response->assertStatus(200);
        $url = '/equipment/update/dim/' . Dimension::all()->last()->id;
        $response = $this->post($url, [
            'dim_type' => 'Internal',
            'dim_name' => 'Width',
            'dim_validate' => 'drafted',
            'dim_value' => '18',
            'dim_unit' => 'cm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'Internal')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Width')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'cm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'drafted',
        ]);

    }

    /**
     * Test Conception Number : 25
     * Update successfully a dimension as to be validated with only a value
     * Type : /  Name : /  Value : 8930 Unit : /
     * Expected result : The dimension is correctly updated in data base
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests
     * Type : External  Name : Length  Value : 29 Unit : mm
     * Eq External Ref / Eq Internal Ref : Example1
     * Eq set : /
     * @return void
     */

    public function test_update_dim_toBeValidated_correctValue()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'to_be_validated',
            'dim_value' => '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value' => '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'drafted',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id
        ]);
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'to_be_validated',
            'dim_value' => '8930',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $url = '/equipment/update/dim/' . Dimension::all()->last()->id;
        $response = $this->post($url, [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'to_be_validated',
            'dim_value' => '8930',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id,
            'dim_value' => 8930,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'to_be_validated',
        ]);

    }

    /**
     * Test Conception Number : 26
     * Update successfully a dimension as drafted with a type, name value and a unit
     * Type : Internal  Name : Width  Value : 18 Unit : cm
     * Expected result : The dimension is correctly updated in data base
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests
     * Type : External  Name : Length  Value : 29 Unit : mm
     * Eq External Ref / Eq Internal Ref : Example1
     * Eq set : /
     * @return void
     */

    public function test_update_dim_toBeValidated_correctValues()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'to_be_validated',
            'dim_value' => '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'to_be_validated',
            'dim_value' => '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'to_be_validated',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'Internal',
            'id' => EnumDimensionType::all()->where('value', '=', 'Internal')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Width',
            'id' => EnumDimensionName::all()->where('value', '=', 'Width')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'cm',
            'id' => EnumDimensionUnit::all()->where('value', '=', 'cm')->first()->id
        ]);
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'Internal',
            'dim_name' => 'Width',
            'dim_validate' => 'to_be_validated',
            'dim_value' => '18',
            'dim_unit' => 'cm'
        ]);
        $response->assertStatus(200);
        $url = '/equipment/update/dim/' . Dimension::all()->last()->id;
        $response = $this->post($url, [
            'dim_type' => 'Internal',
            'dim_name' => 'Width',
            'dim_validate' => 'to_be_validated',
            'dim_value' => '18',
            'dim_unit' => 'cm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'Internal')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Width')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'cm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'to_be_validated',
        ]);

    }

    /**
     * Test Conception Number : 36
     * Update successfully a dimension as validated
     * Type : Internal  Name : Width  Value : 18 Unit : cm
     * Expected result : The dimension is correctly updated in data base
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests
     * Type : External  Name : Length  Value : 29 Unit : mm
     * Eq External Ref / Eq Internal Ref : Example1
     * Eq set : /
     * @return void
     */

    public function test_update_dim_validated_correctValues()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'Internal',
            'id' => EnumDimensionType::all()->where('value', '=', 'Internal')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Width',
            'id' => EnumDimensionName::all()->where('value', '=', 'Width')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'cm',
            'id' => EnumDimensionUnit::all()->where('value', '=', 'cm')->first()->id
        ]);
        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'Internal',
            'dim_name' => 'Width',
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_unit' => 'cm'
        ]);
        $response->assertStatus(200);
        $url = '/equipment/update/dim/' . Dimension::all()->last()->id;
        $response = $this->post($url, [
            'dim_type' => 'Internal',
            'dim_name' => 'Width',
            'dim_validate' => 'validated',
            'dim_value' => '18',
            'dim_unit' => 'cm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'Internal')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Width')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'cm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);

    }

    /**
     * Test Conception Number : 37
     * Update dimension type of a validated equipment successfully
     * Type : Internal  Name : Length  Value : 29 Unit : mm
     * Expected result : The dimension type is correctly updated in the database, the version number of the equipment has been incremented, the attributes qualityVerifier and TechnicalVerifier become NULL and the attribute representing the creation of the life sheet takes the value false
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests
     * Type : External  Name : Length  Value : 29 Unit : mm
     * Eq External Ref / Eq Internal Ref : Example1
     * Eq set : /
     * @return void
     */

    public function test_updateType_dim_validated()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted', 'Test');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'Internal',
            'id' => EnumDimensionType::all()->where('value', '=', 'Internal')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id
        ]);

        $response = $this->post('/equipment/update/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'eq_name' => 'Test',
            'eq_serialNumber' => 'Test',
            'eq_constructor' => 'Test',
            'eq_set' => 'Test',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'Test',
            'eq_mobility' => true,
            'eq_type' => 'internal',
            /* 'eqTemp_lifeSheetCreated' => true,
             'qualityVerifier_id' => '1',
             'technicalVerifier_id' => '1',*/
        ]);

        $response = $this->post('/equipment/validation/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->where('user_pseudo', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->where('user_pseudo', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('equipment', [
            'eq_nbrVersion' => 1,
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 1,
            'eqTemp_lifeSheetCreated' => true,
            'qualityVerifier_id' => User::all()->where('user_pseudo', '=', 'Test')->first()->id,
            'technicalVerifier_id' => User::all()->where('user_pseudo', '=', 'Test')->first()->id,
            'eqTemp_validate' => 'validated',
            'equipment_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,

        ]);

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'Internal',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm',
        ]);
        $response->assertStatus(200);
        $url = '/equipment/update/dim/' . Dimension::all()->last()->id;
        $response = $this->post($url, [
            'dim_type' => 'Internal',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'Internal')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);

        $this->assertDatabaseHas('equipment', [
            'eq_nbrVersion' => 2,
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 2,
            'eqTemp_lifeSheetCreated' => false,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
        ]);

    }


    /**
     * Test Conception Number : 38
     * Update dimension name of a validated equipment successfully
     * Type : External  Name : Width  Value : 29 Unit : mm
     * Expected result : The dimension name is correctly updated in the database, the version number of the equipment has been incremented, the attributes qualityVerifier and TechnicalVerifier become NULL and the attribute representing the creation of the life sheet takes the value false
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests
     * Type : External  Name : Length  Value : 29 Unit : mm
     * Eq External Ref / Eq Internal Ref : Example1
     * Eq set : /
     * @return void
     */

    public function test_updateName_dim_validated()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted', 'Test');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Width',
            'id' => EnumDimensionName::all()->where('value', '=', 'Width')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id
        ]);

        $response = $this->post('/equipment/update/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'eq_name' => 'Test',
            'eq_serialNumber' => 'Test',
            'eq_constructor' => 'Test',
            'eq_set' => 'Test',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'Test',
            'eq_mobility' => true,
            'eq_type' => 'internal',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/validation/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->where('user_pseudo', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->where('user_pseudo', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('equipment', [
            'eq_nbrVersion' => 1,
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 1,
            'eqTemp_lifeSheetCreated' => true,
            'qualityVerifier_id' => User::all()->where('user_pseudo', '=', 'Test')->first()->id,
            'technicalVerifier_id' => User::all()->where('user_pseudo', '=', 'Test')->first()->id,
            'eqTemp_validate' => 'validated',
            'equipment_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,

        ]);

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Width',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm',
        ]);
        $response->assertStatus(200);
        $url = '/equipment/update/dim/' . Dimension::all()->last()->id;
        $response = $this->post($url, [
            'dim_type' => 'External',
            'dim_name' => 'Width',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Width')->first()->id,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);

        $this->assertDatabaseHas('equipment', [
            'eq_nbrVersion' => 2,
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 2,
            'eqTemp_lifeSheetCreated' => false,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,

        ]);

    }

    /**
     * Test Conception Number : 39
     * Update dimension value of a validated equipment successfully
     * Type : External  Name : Length  Value : 30 Unit : mm
     * Expected result : The dimension value is correctly updated in the database,  the version number of the equipment has been incremented, the reason for the update is required and correctly saved, the attributes qualityVerifier and TechnicalVerifier become NULL and the attribute representing the creation of the life sheet takes the value false
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests
     * Type : External  Name : Length  Value : 29 Unit : mm
     * Eq External Ref / Eq Internal Ref : Example1
     * Eq set : /
     * @return void
     */

    public function test_updateValue_dim_validated()
    {
        $this->create_required_enum();
        $this->create_equipment_and_user('Test', 'drafted', 'Test');

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim + 1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => EnumDimensionName::all()->where('value', '=', 'Length')->first()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id
        ]);

        $response = $this->post('/equipment/update/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'eq_name' => 'Test',
            'eq_serialNumber' => 'Test',
            'eq_constructor' => 'Test',
            'eq_set' => 'Test',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'Test',
            'eq_mobility' => true,
            'eq_type' => 'internal',
        ]);

        $response = $this->post('/equipment/validation/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->where('user_pseudo', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->where('user_pseudo', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('equipment', [
            'eq_nbrVersion' => 1,
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 1,
            'eqTemp_lifeSheetCreated' => true,
            'qualityVerifier_id' => User::all()->where('user_pseudo', '=', 'Test')->first()->id,
            'technicalVerifier_id' => User::all()->where('user_pseudo', '=', 'Test')->first()->id,
            'eqTemp_validate' => 'validated',
            'equipment_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,

        ]);

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '30',
            'dim_unit' => 'mm',
        ]);
        $response->assertStatus(200);
        $url = '/equipment/update/dim/' . Dimension::all()->last()->id;
        $response = $this->post($url, [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '30',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => 30,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
            'dim_validate' => 'validated',
        ]);

        $this->assertDatabaseHas('equipment', [
            'eq_nbrVersion' => 2,
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 2,
            'eqTemp_lifeSheetCreated' => false,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,

        ]);

    }


    /**
     * Test Conception Number : 40
     * Update dimension unit of a validated equipment successfully
     * Type : External  Name : Length  Value : 29 Unit : cm
     * Expected result : The dimension value is correctly updated in the database, the version number of the equipment has been incremented, the attributes qualityVerifier and TechnicalVerifier become NULL and the attribute representing the creation of the life sheet takes the value false
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests
     * Type : External  Name : Length  Value : 29 Unit : mm
     * Eq External Ref / Eq Internal Ref : Example1
     * Eq set : /
     * @return void
     */

    public function test_updateUnit_dim_validated()
    {

        $countUser = User::all()->count();
        $response = $this->post('register', [
            'user_firstName' => 'Test',
            'user_lastName' => 'Test',
            'user_pseudo' => 'Test',
            'user_password' => 'TestTestTest',
            'user_confirmation_password' => 'TestTestTest',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countUser + 1, User::all());

        $countEq = Equipment::all()->count();
        $response = $this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq + 1, Equipment::all());

        $countDimType = EnumDimensionType::all()->count();
        $response = $this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType + 1, EnumDimensionType::all());

        $countDimName = EnumDimensionName::all()->count();
        $response = $this->post('/dimension/enum/name/add', [
            'value' => 'Length',
        ]);

        $response->assertStatus(200);
        $this->assertCount($countDimName + 1, EnumDimensionName::all());

        $countDimUnit = EnumDimensionUnit::all()->count();
        $response = $this->post('/dimension/enum/unit/add', [
            'value' => 'mm',
        ]);
        $response = $this->post('/dimension/enum/unit/add', [
            'value' => 'cm',
        ]);
        $this->assertCount($countDimUnit + 2, EnumDimensionUnit::all());

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id - 1,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => EnumDimensionName::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => EnumDimensionUnit::all()->last()->id - 1
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'cm',
            'id' => EnumDimensionUnit::all()->last()->id
        ]);

        $countEqType = EnumEquipmentType::all()->count();
        $response = $this->post('/equipment/enum/type/add', [
            'value' => 'Internal',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countEqType + 1, EnumEquipmentType::all());

        $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
        $response = $this->post('/equipment/enum/massUnit/add', [
            'value' => 'g',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());


        $response = $this->post('/equipment/update/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'eq_name' => 'Test',
            'eq_serialNumber' => 'Test',
            'eq_constructor' => 'Test',
            'eq_set' => 'Test',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'Test',
            'eq_mobility' => true,
            'eq_type' => 'internal',
        ]);

        $response = $this->post('/equipment/validation/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('equipment', [
            'eq_nbrVersion' => 1,
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 1,
            'eqTemp_lifeSheetCreated' => true,
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id,
            'eqTemp_validate' => 'validated',
            'equipment_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,

        ]);

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'cm',
        ]);
        $response->assertStatus(200);
        $url = '/equipment/update/dim/' . Dimension::all()->last()->id;
        $response = $this->post($url, [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'cm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);

        $this->assertDatabaseHas('equipment', [
            'eq_nbrVersion' => 2,
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 2,
            'eqTemp_lifeSheetCreated' => false,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,

        ]);

    }

    /**
     * Test Conception Number : 54
     * Add successfully a new dimension in validated for a validated equipment
     * Type : External  Name : Width  Value : 41 Unit : km
     * Expected result :The dimension is correctly saved in the database,  the version number of the equipment has been incremented, the reason for the update is required and correctly saved, the attributes qualityVerifier and TechnicalVerifier become NULL and the attribute representing the creation of the life sheet takes the value false
     * @return void
     */

    public function test_addFromUpdate_dim()
    {

        $countUser = User::all()->count();
        $response = $this->post('register', [
            'user_firstName' => 'Test',
            'user_lastName' => 'Test',
            'user_pseudo' => 'Test',
            'user_password' => 'TestTestTest',
            'user_confirmation_password' => 'TestTestTest',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countUser + 1, User::all());

        $countEq = Equipment::all()->count();
        $response = $this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq + 1, Equipment::all());

        $countDimType = EnumDimensionType::all()->count();
        $response = $this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType + 1, EnumDimensionType::all());

        $countDimName = EnumDimensionName::all()->count();
        $response = $this->post('/dimension/enum/name/add', [
            'value' => 'Width',
        ]);

        $response->assertStatus(200);
        $this->assertCount($countDimName + 1, EnumDimensionName::all());

        $countDimUnit = EnumDimensionUnit::all()->count();
        $response = $this->post('/dimension/enum/unit/add', [
            'value' => 'km',
        ]);
        $this->assertCount($countDimUnit + 1, EnumDimensionUnit::all());

        $countEqType = EnumEquipmentType::all()->count();
        $response = $this->post('/equipment/enum/type/add', [
            'value' => 'Internal',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countEqType + 1, EnumEquipmentType::all());

        $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
        $response = $this->post('/equipment/enum/massUnit/add', [
            'value' => 'g',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());


        $response = $this->post('/equipment/verif/', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'eq_name' => 'Test',
            'eq_serialNumber' => 'Test',
            'eq_constructor' => 'Test',
            'eq_set' => 'Test',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'Test',
            'eq_mobility' => true,
            'eq_type' => 'Internal',
        ]);
        $response->assertStatus(200);


        $response = $this->post('/equipment/update/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'eq_name' => 'Test',
            'eq_serialNumber' => 'Test',
            'eq_constructor' => 'Test',
            'eq_set' => 'Test',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'Test',
            'eq_mobility' => true,
            'eq_type' => 'Internal',
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('equipment', [
            'eq_nbrVersion' => 1,
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 1,
            'eqTemp_lifeSheetCreated' => true,
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id,
            'eqTemp_validate' => 'validated',
            'equipment_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Width',
            'dim_validate' => 'validated',
            'dim_value' => '41',
            'dim_unit' => 'km'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Width',
            'dim_validate' => 'validated',
            'dim_value' => '41',
            'dim_unit' => 'km',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => 41,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Width',
            'id' => EnumDimensionName::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'km',
            'id' => EnumDimensionUnit::all()->last()->id
        ]);

        $this->assertDatabaseHas('equipment', [
            'eq_nbrVersion' => 2,
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 2,
            'eqTemp_lifeSheetCreated' => false,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,

        ]);

    }

    /**
     * Test Conception Number : 55
     * Consult the dimension of an equipment
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests
     * External    Length    29    mm
     * Internal Width    41    cm
     * Expected result :The equipment has two dimensions with the value : External - length - 29 - mm and Internal - width - 41 - cm
     * @return void
     */

    public function test_consult_dim()
    {

        $countEq = Equipment::all()->count();
        $response = $this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq + 1, Equipment::all());

        $countDimType = EnumDimensionType::all()->count();
        $response = $this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/dimension/enum/type/add', [
            'value' => 'Internal',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType + 2, EnumDimensionType::all());

        $countDimName = EnumDimensionName::all()->count();
        $response = $this->post('/dimension/enum/name/add', [
            'value' => 'Length',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/dimension/enum/name/add', [
            'value' => 'Width',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimName + 2, EnumDimensionName::all());

        $countDimUnit = EnumDimensionUnit::all()->count();
        $response = $this->post('/dimension/enum/unit/add', [
            'value' => 'mm',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/dimension/enum/unit/add', [
            'value' => 'cm',
        ]);
        $this->assertCount($countDimUnit + 2, EnumDimensionUnit::all());

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/dimension/verif', [
            'dim_type' => 'Internal',
            'dim_name' => 'Width',
            'dim_validate' => 'validated',
            'dim_value' => '41',
            'dim_unit' => 'cm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'Internal',
            'dim_name' => 'Width',
            'dim_validate' => 'validated',
            'dim_value' => '41',
            'dim_unit' => 'cm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);

        $this->assertCount($countDim + 2, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id - 1,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id - 1,
            'dim_value' => '29',
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id - 1,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => '41',
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->last()->id - 1
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'Internal',
            'id' => EnumDimensionType::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => EnumDimensionName::all()->last()->id - 1
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Width',
            'id' => EnumDimensionName::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => EnumDimensionUnit::all()->last()->id - 1
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'cm',
            'id' => EnumDimensionUnit::all()->last()->id
        ]);

        $this->assertDatabaseHas('equipment', [
            'eq_nbrVersion' => 1,
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 1,
            'eqTemp_lifeSheetCreated' => false,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,

        ]);

        $response = $this->get('/dimension/send/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                "id" => Dimension::all()->last()->id - 1,
                "dim_value" => 29,
                "dim_name" => "Length",
                "dim_type" => "External",
                "dim_unit" => "mm",
                "dim_validate" => "validated",
            ],
            '1' => [
                "id" => Dimension::all()->last()->id,
                "dim_value" => 41,
                "dim_name" => "Width",
                "dim_type" => "Internal",
                "dim_unit" => "cm",
                "dim_validate" => "validated",
            ],
        ]);
    }

    /**
     * Test Conception Number : 56
     * Consult by type the dimension of an equipment
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests
     * External    Length    29    mm
     * Internal Width    41    cm
     * Expected result :The equipment has two dimensions with the value : External - length - 29 - mm and Internal - width - 41 - cm sorted by type
     * @return void
     */

    public function test_consultByType_dim()
    {

        $countEq = Equipment::all()->count();
        $response = $this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq + 1, Equipment::all());

        $countDimType = EnumDimensionType::all()->count();
        $response = $this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/dimension/enum/type/add', [
            'value' => 'Internal',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType + 2, EnumDimensionType::all());

        $countDimName = EnumDimensionName::all()->count();
        $response = $this->post('/dimension/enum/name/add', [
            'value' => 'Length',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/dimension/enum/name/add', [
            'value' => 'Width',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimName + 2, EnumDimensionName::all());

        $countDimUnit = EnumDimensionUnit::all()->count();
        $response = $this->post('/dimension/enum/unit/add', [
            'value' => 'mm',
        ]);
        $response->assertStatus(200);
        $response = $this->post('/dimension/enum/unit/add', [
            'value' => 'cm',
        ]);
        $this->assertCount($countDimUnit + 2, EnumDimensionUnit::all());

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/dimension/verif', [
            'dim_type' => 'Internal',
            'dim_name' => 'Width',
            'dim_validate' => 'validated',
            'dim_value' => '41',
            'dim_unit' => 'cm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'Internal',
            'dim_name' => 'Width',
            'dim_validate' => 'validated',
            'dim_value' => '41',
            'dim_unit' => 'cm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);

        $this->assertCount($countDim + 2, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id - 1,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id - 1,
            'dim_value' => '29',
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id - 1,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => '41',
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->last()->id - 1
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'Internal',
            'id' => EnumDimensionType::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => EnumDimensionName::all()->last()->id - 1
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Width',
            'id' => EnumDimensionName::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => EnumDimensionUnit::all()->last()->id - 1
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'cm',
            'id' => EnumDimensionUnit::all()->last()->id
        ]);

        $this->assertDatabaseHas('equipment', [
            'eq_nbrVersion' => 1,
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 1,
            'eqTemp_lifeSheetCreated' => false,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,

        ]);

        $response = $this->get('/dimension/send/ByType/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id);
        $response->assertStatus(200);
        $response->assertJson([
            '0' => [
                "type" => 'External',
                "dimensions" => [
                    '0' => [
                        "id" => Dimension::all()->last()->id - 1,
                        "dim_value" => 29,
                        "dim_name" => "Length",
                        "dim_type" => "External",
                        "dim_unit" => "mm",
                        "dim_validate" => "validated",
                    ]
                ]
            ],
            '1' => [
                "type" => 'Internal',
                "dimensions" => [
                    '0' => [
                        "id" => Dimension::all()->last()->id,
                        "dim_value" => 41,
                        "dim_name" => "Width",
                        "dim_type" => "Internal",
                        "dim_unit" => "cm",
                        "dim_validate" => "validated",
                    ]
                ]
            ]
        ]);
    }

    /**
     * Test Conception Number : 57
     * Delete the dimension previously created
     * Expected result :The dimension is correctly saved in the database,  the version number of the equipment has been incremented, the reason for the update is required and correctly saved, the attributes qualityVerifier and TechnicalVerifier become NULL and the attribute representing the creation of the life sheet takes the value false
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests
     * Type : External  Name : Length  Value : 29 Unit : mm
     * @return void
     */

    public function test_delete_dim()
    {

        $countEq = Equipment::all()->count();
        $response = $this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq + 1, Equipment::all());

        $countDimType = EnumDimensionType::all()->count();
        $response = $this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType + 1, EnumDimensionType::all());

        $countDimName = EnumDimensionName::all()->count();
        $response = $this->post('/dimension/enum/name/add', [
            'value' => 'Length',
        ]);

        $response->assertStatus(200);
        $this->assertCount($countDimName + 1, EnumDimensionName::all());

        $countDimUnit = EnumDimensionUnit::all()->count();
        $response = $this->post('/dimension/enum/unit/add', [
            'value' => 'mm',
        ]);
        $this->assertCount($countDimUnit + 1, EnumDimensionUnit::all());

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);

        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => EnumDimensionName::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => EnumDimensionUnit::all()->last()->id
        ]);

        $countDim = Dimension::all()->count();
        $response = $this->post('/equipment/delete/dim/' . Dimension::all()->last()->id, [
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim - 1, Dimension::all());

        $this->assertDatabaseHas('equipment', [
            'eq_nbrVersion' => 1,
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 1,
            'eqTemp_lifeSheetCreated' => false,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
        ]);


    }

    /**
     * Test Conception Number : 58
     * Delete the dimension previously created of an validated equipment
     * Type : External  Name : Width  Value : 41 Unit : km
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests
     * Type : External  Name : Length  Value : 29 Unit : mm
     * Expected result :TThe dimension has been deleted successfully and the version number of the equipment has been incremented, the attributes qualityVerifier and TechnicalVerifier become NULL and the attribute representing the creation of the life sheet takes the value false
     * @return void
     */

    public function test_delete_dimFromValidatedEq()
    {

        $countUser = User::all()->count();
        $response = $this->post('register', [
            'user_firstName' => 'Test',
            'user_lastName' => 'Test',
            'user_pseudo' => 'Test',
            'user_password' => 'TestTestTest',
            'user_confirmation_password' => 'TestTestTest',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countUser + 1, User::all());

        $countEq = Equipment::all()->count();
        $response = $this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq + 1, Equipment::all());

        $countDimType = EnumDimensionType::all()->count();
        $response = $this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType + 1, EnumDimensionType::all());

        $countDimName = EnumDimensionName::all()->count();
        $response = $this->post('/dimension/enum/name/add', [
            'value' => 'Length',
        ]);

        $response->assertStatus(200);
        $this->assertCount($countDimName + 1, EnumDimensionName::all());

        $countDimUnit = EnumDimensionUnit::all()->count();
        $response = $this->post('/dimension/enum/unit/add', [
            'value' => 'mm',
        ]);
        $this->assertCount($countDimUnit + 1, EnumDimensionUnit::all());

        $countEqType = EnumEquipmentType::all()->count();
        $response = $this->post('/equipment/enum/type/add', [
            'value' => 'Internal',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countEqType + 1, EnumEquipmentType::all());

        $countEqMassUnit = EnumEquipmentMassUnit::all()->count();
        $response = $this->post('/equipment/enum/massUnit/add', [
            'value' => 'g',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countEqMassUnit + 1, EnumEquipmentMassUnit::all());


        $response = $this->post('/equipment/verif/', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'eq_name' => 'Test',
            'eq_serialNumber' => 'Test',
            'eq_constructor' => 'Test',
            'eq_set' => 'Test',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'Test',
            'eq_mobility' => true,
            'eq_type' => 'Internal',
        ]);
        $response->assertStatus(200);


        $response = $this->post('/equipment/update/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'eq_name' => 'Test',
            'eq_serialNumber' => 'Test',
            'eq_constructor' => 'Test',
            'eq_set' => 'Test',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'Test',
            'eq_mobility' => true,
            'eq_type' => 'Internal',
        ]);
        $response->assertStatus(200);

        $countDim = Dimension::all()->count();
        $response = $this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value' => '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => '29',
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id)->first()->id,
            'dim_validate' => 'validated',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id' => EnumDimensionType::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id' => EnumDimensionName::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id' => EnumDimensionUnit::all()->last()->id
        ]);


        $response = $this->post('/equipment/validation/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/equipment/validation/' . Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);


        $this->assertDatabaseHas('equipment', [
            'eq_nbrVersion' => 1,
        ]);

        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 1,
            'eqTemp_lifeSheetCreated' => true,
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id,
            'eqTemp_validate' => 'validated',
            'equipment_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);

        $countDim = Dimension::all()->count();
        $response = $this->post('/equipment/delete/dim/' . Dimension::all()->last()->id, [
            'eq_id' => Equipment::all()->where('eq_internalReference', '=', 'Test')->first()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim - 1, Dimension::all());


        $this->assertDatabaseHas('equipment', [
            'eq_nbrVersion' => 2,
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'eqTemp_version' => 2,
            'eqTemp_lifeSheetCreated' => false,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,

        ]);

    }


}

?>

