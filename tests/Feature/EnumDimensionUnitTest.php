<?php

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
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EnumDimensionUnitTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test Conception Number: 1
     * Try to add a non-existent unit in the database
     * Unit: Unit
     * Expected result: The unit is correctly added to the database
     * @return void
     */
    public function test_add_non_existent_unit() {
        $oldCOunt = EnumDimensionUnit::all()->count();
        $response = $this->post('/dimension/enum/unit/add', [
            'value' => 'Unit'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(EnumDimensionUnit::all()->count(), $oldCOunt+1);
    }

    /**
     * Test Conception Number: 2
     * Try to add two time the same unit in the database
     * Unit: Exist
     * Expected result: Receiving an error:
     *                                      "The value of the field for the new dimension unit already exist in the data base"
     * @return void
     */
    public function test_add_two_time_same_unit() {
        $oldCOunt = EnumDimensionUnit::all()->count();
        $response = $this->post('/dimension/enum/unit/add', [
            'value' => 'Exist'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(EnumDimensionUnit::all()->count(), $oldCOunt+1);
        $response = $this->post('/dimension/enum/unit/add', [
            'value' => 'Exist'
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_dim_unit' => [
                "The value of the field for the new dimension unit already exist in the data base"
            ]
        ]);
        $this->assertEquals(EnumDimensionUnit::all()->count(), $oldCOunt+1);
    }

    public function requiredForTest() {
        // Add the different enum of the name if they didn't already exist in the database
        if (EnumDimensionName::all()->where('value', '=', 'Name')->count() === 0) {
            $countDimName=EnumDimensionName::all()->count();
            $response=$this->post('/dimension/enum/name/add', [
                'value' => 'Name',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimName+1, EnumDimensionName::all());
        }
        if (EnumDimensionName::all()->where('value', '=', 'Exist')->count() === 0) {
            $countDimName=EnumDimensionName::all()->count();
            $response=$this->post('/dimension/enum/name/add', [
                'value' => 'Exist',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimName+1, EnumDimensionName::all());
        }
        // Add the prerequisite enum for the equipment creation if they didn't already exist in the database
        if (EnumDimensionType::all()->where('value', '=', 'External')->count() === 0) {
            $countDimType=EnumDimensionType::all()->count();
            $response=$this->post('/dimension/enum/type/add', [
                'value' => 'External',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimType+1, EnumDimensionType::all());
        }
        if (EnumDimensionType::all()->where('value', '=', 'Internal')->count() === 0) {
            $countDimType=EnumDimensionType::all()->count();
            $response=$this->post('/dimension/enum/type/add', [
                'value' => 'Internal',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimType+1, EnumDimensionType::all());
        }
        if (EnumDimensionType::all()->where('value', '=', 'External')->count() === 0) {
            $countDimType=EnumDimensionType::all()->count();
            $response=$this->post('/dimension/enum/type/add', [
                'value' => 'External',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimType+1, EnumDimensionType::all());
        }
        if (EnumDimensionUnit::all()->where('value', '=', 'Unit')->count() === 0) {
            $countDimUnit=EnumDimensionUnit::all()->count();
            $response=$this->post('/dimension/enum/unit/add', [
                'value' => 'Unit',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimUnit+1, EnumDimensionUnit::all());
        }
        if (EnumDimensionUnit::all()->where('value', '=', 'Exist')->count() === 0) {
            $countDimUnit=EnumDimensionUnit::all()->count();
            $response=$this->post('/dimension/enum/unit/add', [
                'value' => 'Exist',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countDimUnit+1, EnumDimensionUnit::all());
        }
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'g')->count() === 0) {
            $countEqMassUnit=EnumEquipmentMassUnit::all()->count();
            $response=$this->post('/equipment/enum/massUnit/add', [
                'value' => 'g',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit+1, EnumEquipmentMassUnit::all());
        }
        if (EnumEquipmentType::all()->where('value', '=', 'Balance')->count() === 0) {
            $countEqType=EnumEquipmentType::all()->count();
            $response=$this->post('/equipment/enum/type/add', [
                'value' => 'Balance',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqType+1, EnumEquipmentType::all());
        }
        // Add the user to validate the equipment
        if (User::all()->where('user_firstName', '=', 'Verifier')->count() === 0) {
            $countUser=User::all()->count();
            $response=$this->post('register', [
                'user_firstName' => 'Verifier',
                'user_lastName' => 'Verifier',
                'user_pseudo' => 'Verifier',
                'user_password' => 'VerifierVerifier',
                'user_confirmation_password' => 'VerifierVerifier',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countUser+1, User::all());
        }
    }

    /**
     * Test Conception Number: 3
     * Analyze the enum 'Test' and expecting the correct data
     * Unit: Unit
     * Expected result: The data contain one validated equipment and one not validated
     * @returns void
     */
    public function test_analyze_data() {
        $this->requiredForTest();

        $countEquipment = Equipment::all()->count();
        $response=$this->post('/equipment/add', [
            'eq_validate' => 'drafted',
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
            'eq_type' => 'Balance',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'TestAnalyze',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'Unit'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'Unit',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $response=$this->post('/equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'TestvalidatedUnit',
            'eq_externalReference' => 'TestvalidatedUnit',
            'eq_name' => 'TestvalidatedUnit',
            'eq_serialNumber' => 'TestvalidatedUnit',
            'eq_constructor' => 'TestvalidatedUnit',
            'eq_set' => 'TestvalidatedUnit',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'TestvalidatedUnit',
            'eq_mobility' => true,
            'eq_type' => 'Balance',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+2, Equipment::all()->count());
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'Unit'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'Unit',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $response->assertStatus(200);

        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('dimension/enum/unit/analyze/' . EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'equipments',
            'validated_eq',
        ]);
        $response->assertJson([
            'id' => EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id,
            'equipments' => [
                '0' => [
                    "eqTemp_id" => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->last()->id)->first()->id,
                    "name" => "Test",
                    "internalReference" => "Test"
                ],
            ],
            'validated_eq' => [
                '0' => [
                    "eqTemp_id" => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'TestvalidatedUnit')->first()->id)->first()->id,
                    "name" => "TestvalidatedUnit",
                    "internalReference" => "TestvalidatedUnit"
                ]
            ],
        ]);
    }

    /**
     * Test Conception Number: 4
     * Try to update an enum linked to drafted equipment with a non-existent unit in the database
     * Unit: TestDrafted
     * Expected result: The unit is correctly updated in the database
     * @returns void
     */
    public function test_update_enum_linked_to_drafted_with_non_existent_unit() {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response=$this->post('/equipment/add', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'TestUpdateEnum1',
            'eq_externalReference' => 'TestUpdateEnum1',
            'eq_name' => 'TestUpdateEnum1',
            'eq_serialNumber' => 'TestUpdateEnum1',
            'eq_constructor' => 'TestUpdateEnum1',
            'eq_set' => 'TestUpdateEnum1',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'TestUpdateEnum1',
            'eq_mobility' => true,
            'eq_type' => 'Balance',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'Unit'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'Unit',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $oldId = EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id;
        $response = $this->post('dimension/enum/unit/verif/' . EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id, [
            'value' => 'TestDrafted'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/dimension/enum/unit/update/'.EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id, [
            'value' => 'TestDrafted',
            'validated_eq' => []
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $newId = EnumDimensionUnit::all()->where('value', '=', 'TestDrafted')->first()->id;
        $this->assertEquals($oldId, $newId);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'TestDrafted',
        ]);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'TestDrafted')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
    }

    /**
     * Test Conception Number: 5
     * Try to update an enum linked to to_be_validated equipment with a non-existent unit in the database
     * Unit: TestToBeValidated
     * Expected result: The unit is correctly updated in the database
     * @returns void
     */
    public function test_update_enum_linked_to_toBeValidated_with_non_existent_unit() {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response=$this->post('/equipment/add', [
            'eq_validate' => 'to_be_validated',
            'eq_internalReference' => 'TestUpdateEnum2',
            'eq_externalReference' => 'TestUpdateEnum2',
            'eq_name' => 'TestUpdateEnum2',
            'eq_serialNumber' => 'TestUpdateEnum2',
            'eq_constructor' => 'TestUpdateEnum2',
            'eq_set' => 'TestUpdateEnum2',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'TestUpdateEnum2',
            'eq_mobility' => true,
            'eq_type' => 'Balance',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'Unit'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'Unit',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $response = $this->post('dimension/enum/unit/verif/' . EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id, [
            'value' => 'TestToBeValidated'
        ]);
        $response->assertStatus(200);
        $oldId = EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id;
        $response = $this->post('/dimension/enum/unit/update/'.EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id, [
            'value' => 'TestToBeValidated',
            'validated_eq' => []
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $newId = EnumDimensionUnit::all()->where('value', '=', 'TestToBeValidated')->first()->id;
        $this->assertEquals($oldId, $newId);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'TestToBeValidated',
        ]);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'TestToBeValidated')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
    }

    /**
     * Test Conception Number: 6
     * Try to update an enum linked to validated equipment with a non-existent unit in the database
     * Unit: TestvalidatedUnit
     * Expected result: The unit is correctly updated in the database, and a history is created in the database
     * @returns void
     */
    public function test_update_enum_linked_to_validated_with_non_existent_unit() {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response=$this->post('/equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'TestUpdateEnum3',
            'eq_externalReference' => 'TestUpdateEnum3',
            'eq_name' => 'TestUpdateEnum3',
            'eq_serialNumber' => 'TestUpdateEnum3',
            'eq_constructor' => 'TestUpdateEnum3',
            'eq_set' => 'TestUpdateEnum3',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'TestUpdateEnum3',
            'eq_mobility' => true,
            'eq_type' => 'Balance',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'Unit'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'Unit',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $response->assertStatus(200);

        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_validate' => 'validated',
            'eqTemp_mass' => 12,
            'eqTemp_remarks' => 'TestUpdateEnum3',
            'eqTemp_mobility' => true,
            'eqTemp_version' => 1,
            'qualityVerifier_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
            'technicalVerifier_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $oldId = EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id;
        $response = $this->post('dimension/enum/unit/analyze/' . EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id);
        $response->assertStatus(200);
        $tab = array();
        foreach (json_decode($response->getContent())->validated_eq as $eq) {
            array_push($tab, array(
                'eqTemp_id' => $eq->eqTemp_id,
                'name' => $eq->name,
                'internalReference' => $eq->internalReference,
            ));
        }
        $response = $this->post('dimension/enum/unit/verif/' . EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id, [
            'value' => 'TestvalidatedUnit'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/dimension/enum/unit/update/'.EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id, [
            'value' => 'TestvalidatedUnit',
            'validated_eq' => $tab,
            'history_reasonUpdate' => 'TestUpdateEnum3',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Dimension::all());
        $newId = EnumDimensionUnit::all()->where('value', '=', 'TestvalidatedUnit')->first()->id;
        $this->assertEquals($oldId, $newId);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'TestvalidatedUnit',
        ]);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'TestvalidatedUnit')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_validate' => 'validated',
            'eqTemp_mass' => 12,
            'eqTemp_remarks' => 'TestUpdateEnum3',
            'eqTemp_mobility' => true,
            'eqTemp_version' => 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
        ]);
    }

    /**
     * Test Conception Number: 7
     * Try to update an enum linked to equipment with an existent unit in the database
     * Unit: /
     * Expected result: Receiving an error:
     *                                      "The value of the field for the dimension unit already exist in the data base"
     * @returns void
     */
    public function test_update_enum_with_existant_value() {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response=$this->post('/equipment/add', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'TestUpdateEnum4',
            'eq_externalReference' => 'TestUpdateEnum4',
            'eq_name' => 'TestUpdateEnum4',
            'eq_serialNumber' => 'TestUpdateEnum4',
            'eq_constructor' => 'TestUpdateEnum4',
            'eq_set' => 'TestUpdateEnum4',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'TestUpdateEnum4',
            'eq_mobility' => true,
            'eq_type' => 'Balance',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'Unit'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'Unit',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $response = $this->post('dimension/enum/unit/verif/' . EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id, [
            'value' => 'Exist'
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_dim_unit' => 'The value of the field for the dimension unit already exist in the data base'
        ]);
    }

    /**
     * Test Conception Number: 8
     * Try to delete an enum not linked to an equipment
     * Unit: /
     * Expected result: The unit is correctly deleted in the database
     * @returns void
     */
    public function test_delete_enum_not_linked() {
        $this->requiredForTest();
        $countEnumDimUnit = EnumDimensionUnit::all()->count();
        $response = $this->post('/dimension/enum/unit/delete/'.EnumDimensionUnit::all()->where('value', '=', 'Exist')->first()->id);
        $response->assertStatus(200);
        $this->assertCount($countEnumDimUnit-1, EnumDimensionUnit::all());
    }

    /**
     * Test Conception Number: 9
     * Try to delete an enum linked to an equipment
     * Unit: TestvalidatedUnit
     * Expected result: Receiving an error:
     *                                      "This value is already used in the data base so you can't delete it"
     * @returns void
     */
    public function test_delete_enum_linked() {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response=$this->post('/equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'TestUpdateEnum3',
            'eq_externalReference' => 'TestUpdateEnum3',
            'eq_name' => 'TestUpdateEnum3',
            'eq_serialNumber' => 'TestUpdateEnum3',
            'eq_constructor' => 'TestUpdateEnum3',
            'eq_set' => 'TestUpdateEnum3',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'TestUpdateEnum3',
            'eq_mobility' => true,
            'eq_type' => 'Balance',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'Unit'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Name',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'Unit',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $response->assertStatus(200);

        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('equipment_temps', [
            'equipment_id' => Equipment::all()->last()->id,
            'eqTemp_validate' => 'validated',
            'eqTemp_mass' => 12,
            'eqTemp_remarks' => 'TestUpdateEnum3',
            'eqTemp_mobility' => true,
            'eqTemp_version' => 1,
            'qualityVerifier_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
            'technicalVerifier_id' => User::all()->where('user_firstName', '=', 'Verifier')->last()->id,
        ]);
        $response = $this->post('/dimension/enum/unit/delete/'.EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_dim_unit' => 'This value is already used in the data base so you can\'t delete it'
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'Unit',
        ]);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'Name')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
    }

    /**
     * Test Conception Number: 10
     * Try to consult the enum list
     * Unit: TestvalidatedUnit
     * Expected result: The enum list is correct, and we receive all the data
     * @returns void
     */
    /*public function test_consult_enum() {
        $this->requiredForTest();
        $response = $this->get('/dimension/enum/unit');
        $response->assertJson([
            0 => [
                'id' => EnumDimensionUnit::all()->where('value', '=', 'cm')->first()->id,
                'value' => 'cm',
                'id_enum' => 'DimensionUnit'
            ],
            1 => [
                'id' => EnumDimensionUnit::all()->where('value', '=', 'Exist')->first()->id,
                'value' => 'Exist',
                'id_enum' => 'DimensionUnit'
            ],
            2 => [
                'id' => EnumDimensionUnit::all()->where('value', '=', 'km')->first()->id,
                'value' => 'km',
                'id_enum' => 'DimensionUnit'
            ],
            3 => [
                'id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
                'value' => 'mm',
                'id_enum' => 'DimensionUnit'
            ],
            4 => [
                'id' => EnumDimensionUnit::all()->where('value', '=', 'TestDrafted')->first()->id,
                'value' => 'TestDrafted',
                'id_enum' => 'DimensionUnit'
            ],
            5 => [
                'id' => EnumDimensionUnit::all()->where('value', '=', 'TestToBeValidated')->first()->id,
                'value' => 'TestToBeValidated',
                'id_enum' => 'DimensionUnit'
            ],
            6 => [
                'id' => EnumDimensionUnit::all()->where('value', '=', 'TestvalidatedUnit')->first()->id,
                'value' => 'TestvalidatedUnit',
                'id_enum' => 'DimensionUnit'
            ],
            7 => [
                'id' => EnumDimensionUnit::all()->where('value', '=', 'Unit')->first()->id,
                'value' => 'Unit',
                'id_enum' => 'DimensionUnit'
            ],
        ]);
    }*/
}
