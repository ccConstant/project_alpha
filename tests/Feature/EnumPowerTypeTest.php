<?php

namespace Tests\Feature;

use App\Models\SW01\EnumEquipmentMassUnit;
use App\Models\SW01\EnumEquipmentType;
use App\Models\SW01\EnumPowerType;
use App\Models\SW01\Equipment;
use App\Models\SW01\EquipmentTemp;
use App\Models\SW01\Power;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EnumPowerTypeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test Conception Number: 1
     * Try to add a non-existent type in the database
     * Type: Type
     * Expected result: The type is correctly added to the database
     * @return void
     */
    public function test_add_non_existent_type() {
        $oldCOunt = EnumPowerType::all()->count();
        $response = $this->post('/power/enum/type/add', [
            'value' => 'Type'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(EnumPowerType::all()->count(), $oldCOunt+1);
    }

    /**
     * Test Conception Number: 2
     * Try to add two time the same type in the database
     * Type: Exist
     * Expected result: Receiving an error:
     *                                      "The value of the field for the new power type already exist in the data base"
     * @return void
     */
    public function test_add_two_time_same_type() {
        $oldCOunt = EnumPowerType::all()->count();
        $response = $this->post('/power/enum/type/add', [
            'value' => 'Exist'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(EnumPowerType::all()->count(), $oldCOunt+1);
        $response = $this->post('/power/enum/type/add', [
            'value' => 'Exist'
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_pow_type' => [
                "The value of the field for the new power type already exist in the data base"
            ]
        ]);
        $this->assertEquals(EnumPowerType::all()->count(), $oldCOunt+1);
    }

    public function requiredForTest() {
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
        if (EnumPowerType::all()->where('value', '=', 'Type')->count() === 0) {
            $countPowerType=EnumPowerType::all()->count();
            $response=$this->post('/power/enum/type/add', [
                'value' => 'Type',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countPowerType+1, EnumPowerType::all());
        }
        if (EnumPowerType::all()->where('value', '=', 'Exist')->count() === 0) {
            $countPowerType=EnumPowerType::all()->count();
            $response=$this->post('/power/enum/type/add', [
                'value' => 'Exist',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countPowerType+1, EnumPowerType::all());
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
     * Type: Type
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
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'validated',
            'pow_type' => 'Type',
            'pow_name' => 'three',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Type',
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertCount($countPower+1, Power::all());
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'enumPowerType_id' => EnumPowerType::all()->where('value', '=', 'Type')->first()->id,
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id
        ]);
        $response=$this->post('/equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'TestvalidatedPowType',
            'eq_externalReference' => 'TestvalidatedPowType',
            'eq_name' => 'TestvalidatedPowType',
            'eq_serialNumber' => 'TestvalidatedPowType',
            'eq_constructor' => 'TestvalidatedPowType',
            'eq_set' => 'TestvalidatedPowType',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'TestvalidatedPowType',
            'eq_mobility' => true,
            'eq_type' => 'Balance',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+2, Equipment::all()->count());
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Type',
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Type',
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertCount($countPower+1, Power::all());
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'enumPowerType_id' => EnumPowerType::all()->where('value', '=', 'Type')->first()->id,
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id
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
        $response = $this->post('power/enum/type/analyze/' . EnumPowerType::all()->where('value', '=', 'Type')->first()->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'equipments',
            'validated_eq',
        ]);
        $response->assertJson([
            'id' => EnumPowerType::all()->where('value', '=', 'Type')->first()->id,
            'equipments' => [
                '0' => [
                    "eqTemp_id" => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'Test')->last()->id)->first()->id,
                    "name" => "Test",
                    "internalReference" => "Test"
                ],
            ],
            'validated_eq' => [
                '0' => [
                    "eqTemp_id" => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->where('eq_internalReference', '=', 'TestvalidatedPowType')->first()->id)->first()->id,
                    "name" => "TestvalidatedPowType",
                    "internalReference" => "TestvalidatedPowType"
                ]
            ],
        ]);
    }

    /**
     * Test Conception Number: 4
     * Try to update an enum linked to drafted equipment with a non-existent type in the database
     * Type: TestDrafted
     * Expected result: The type is correctly updated in the database
     * @returns void
     */
    public function test_update_enum_linked_to_drafted_with_non_existent_type() {
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
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Type',
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Type',
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertCount($countPower+1, Power::all());
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'enumPowerType_id' => EnumPowerType::all()->where('value', '=', 'Type')->first()->id,
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id
        ]);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $oldId = EnumPowerType::all()->where('value', '=', 'Type')->first()->id;
        $response = $this->post('power/enum/type/verif/' . EnumPowerType::all()->where('value', '=', 'Type')->first()->id, [
            'value' => 'TestDrafted'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/power/enum/type/update/'.EnumPowerType::all()->where('value', '=', 'Type')->first()->id, [
            'value' => 'TestDrafted',
            'validated_eq' => []
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $newId = EnumPowerType::all()->where('value', '=', 'TestDrafted')->first()->id;
        $this->assertEquals($oldId, $newId);
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'TestDrafted',
        ]);
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'enumPowerType_id' => EnumPowerType::all()->where('value', '=', 'TestDrafted')->first()->id,
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id
        ]);
    }

    /**
     * Test Conception Number: 5
     * Try to update an enum linked to to_be_validated equipment with a non-existent type in the database
     * Type: TestToBeValidated
     * Expected result: The type is correctly updated in the database
     * @returns void
     */
    public function test_update_enum_linked_to_toBeValidated_with_non_existent_type() {
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
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Type',
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Type',
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertCount($countPower+1, Power::all());
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'enumPowerType_id' => EnumPowerType::all()->where('value', '=', 'Type')->first()->id,
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id
        ]);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $response = $this->post('power/enum/type/verif/' . EnumPowerType::all()->where('value', '=', 'Type')->first()->id, [
            'value' => 'TestToBeValidated'
        ]);
        $response->assertStatus(200);
        $oldId = EnumPowerType::all()->where('value', '=', 'Type')->first()->id;
        $response = $this->post('/power/enum/type/update/'.EnumPowerType::all()->where('value', '=', 'Type')->first()->id, [
            'value' => 'TestToBeValidated',
            'validated_eq' => []
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $newId = EnumPowerType::all()->where('value', '=', 'TestToBeValidated')->first()->id;
        $this->assertEquals($oldId, $newId);
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'TestToBeValidated',
        ]);
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'enumPowerType_id' => EnumPowerType::all()->where('value', '=', 'TestToBeValidated')->first()->id,
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id
        ]);
    }

    /**
     * Test Conception Number: 6
     * Try to update an enum linked to validated equipment with a non-existent type in the database
     * Type: TestvalidatedPowType
     * Expected result: The type is correctly updated in the database, and a history is created in the database
     * @returns void
     */
    public function test_update_enum_linked_to_validated_with_non_existent_type() {
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
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Type',
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Type',
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'enumPowerType_id' => EnumPowerType::all()->where('value', '=', 'Type')->first()->id,
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id
        ]);
        $this->assertCount($countPower+1, Power::all());
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
        $oldId = EnumPowerType::all()->where('value', '=', 'Type')->first()->id;
        $response = $this->post('power/enum/type/analyze/' . EnumPowerType::all()->where('value', '=', 'Type')->first()->id);
        $response->assertStatus(200);
        $tab = array();
        foreach (json_decode($response->getContent())->validated_eq as $eq) {
            array_push($tab, array(
                'eqTemp_id' => $eq->eqTemp_id,
                'name' => $eq->name,
                'internalReference' => $eq->internalReference,
            ));
        }
        $response = $this->post('power/enum/type/verif/' . EnumPowerType::all()->where('value', '=', 'Type')->first()->id, [
            'value' => 'TestvalidatedPowType'
        ]);
        $response->assertStatus(200);
        print_r($response->getContent());
        $response = $this->post('/power/enum/type/update/'.EnumPowerType::all()->where('value', '=', 'Type')->first()->id, [
            'value' => 'TestvalidatedPowType',
            'validated_eq' => $tab,
            'history_reasonUpdate' => 'TestUpdateEnum3',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countPower+1, Power::all());
        $newId = EnumPowerType::all()->where('value', '=', 'TestvalidatedPowType')->first()->id;
        $this->assertEquals($oldId, $newId);
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'TestvalidatedPowType',
        ]);
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'enumPowerType_id' => EnumPowerType::all()->where('value', '=', 'TestvalidatedPowType')->first()->id,
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id
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
     * Try to update an enum linked to equipment with an existent type in the database
     * Type: /
     * Expected result: Receiving an error:
     *                                      "The value of the field for the power type already exist in the data base"
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
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Type',
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Type',
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'enumPowerType_id' => EnumPowerType::all()->where('value', '=', 'Type')->first()->id,
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id
        ]);
        $this->assertCount($countPower+1, Power::all());
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $response = $this->post('/power/enum/type/verif/' . EnumPowerType::all()->where('value', '=', 'Type')->first()->id, [
            'value' => 'Exist'
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_pow_type' => 'The value of the field for the power type already exist in the data base'
        ]);
    }

    /**
     * Test Conception Number: 8
     * Try to delete an enum not linked to an equipment
     * Type: /
     * Expected result: The type is correctly deleted in the database
     * @returns void
     */
    public function test_delete_enum_not_linked() {
        $this->requiredForTest();
        $countEnumDimType = EnumPowerType::all()->count();
        $response = $this->post('/power/enum/type/delete/'.EnumPowerType::all()->where('value', '=', 'Exist')->first()->id);
        $response->assertStatus(200);
        $this->assertCount($countEnumDimType-1, EnumPowerType::all());
    }

    /**
     * Test Conception Number: 9
     * Try to delete an enum linked to an equipment
     * Type: TestvalidatedPowType
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
        $countPower = Power::all()->count();
        $response = $this->post('/power/verif', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Type',
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/pow', [
            'pow_validate' => 'drafted',
            'pow_type' => 'Type',
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'eq_id' => Equipment::all()->last()->id
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'enumPowerType_id' => EnumPowerType::all()->where('value', '=', 'Type')->first()->id,
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id
        ]);
        $this->assertCount($countPower+1, Power::all());
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
        $response = $this->post('/power/enum/type/delete/'.EnumPowerType::all()->where('value', '=', 'Type')->first()->id);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_pow_type' => 'This value is already used in the data base so you can\'t delete it'
        ]);
        $this->assertDatabaseHas('enum_power_types', [
            'value' => 'Type',
        ]);
        $this->assertDatabaseHas('powers', [
            'pow_validate' => 'drafted',
            'enumPowerType_id' => EnumPowerType::all()->where('value', '=', 'Type')->first()->id,
            'pow_name' => 'Name',
            'pow_value' => 220,
            'pow_unit' => 'V',
            'pow_consumptionValue' => 18,
            'pow_consumptionUnit' => 'kwH',
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id
        ]);
    }

    /**
     * Test Conception Number: 10
     * Try to consult the enum list
     * Type: TestvalidatedPowType
     * Expected result: The enum list is correct, and we receive all the data
     * @returns void
     */
    /*public function test_consult_enum() {
        $this->requiredForTest();
        $response = $this->get('/power/enum/type');
        $response->assertJson([
            0 => [
                'id' => EnumPowerType::all()->where('value', '=', 'Exist')->first()->id,
                'value' => 'Exist',
                'id_enum' => 'PowerType'
            ],
            1 => [
                'id' => EnumPowerType::all()->where('value', '=', 'TestDrafted')->first()->id,
                'value' => 'TestDrafted',
                'id_enum' => 'PowerType'
            ],
            2 => [
                'id' => EnumPowerType::all()->where('value', '=', 'TestToBeValidated')->first()->id,
                'value' => 'TestToBeValidated',
                'id_enum' => 'PowerType'
            ],
            3 => [
                'id' => EnumPowerType::all()->where('value', '=', 'TestvalidatedPowType')->first()->id,
                'value' => 'TestvalidatedPowType',
                'id_enum' => 'PowerType'
            ],
            4 => [
                'id' => EnumPowerType::all()->where('value', '=', 'Type')->first()->id,
                'value' => 'Type',
                'id_enum' => 'PowerType'
            ],
        ]);
    }*/
}
