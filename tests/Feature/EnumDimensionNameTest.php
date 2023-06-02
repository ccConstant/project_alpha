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

class EnumDimensionNameTest extends TestCase
{

    use RefreshDatabase;
    /**
     * Test Conception Number: 1
     * Try to add a non-existent name in the database
     * Name : Test
     * Expected result: The name is correctly added to the database
     * @return void
     */
    public function test_add_non_existent_name() {
        $oldCOunt = EnumDimensionName::all()->count();
        $response = $this->post('/dimension/enum/name/add', [
            'value' => 'Test'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(EnumDimensionName::all()->count(), $oldCOunt+1);
    }

    /**
     * Test Conception Number: 2
     * Try to add two time the same name in the database
     * Name : Exist
     * Expected result: Receiving an error:
     *                                      "The value of the field for the new dimension name already exist in the data base"
     * @return void
     */
    public function test_add_two_time_same_name() {
        $oldCOunt = EnumDimensionName::all()->count();
        $response = $this->post('/dimension/enum/name/add', [
            'value' => 'Exist'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(EnumDimensionName::all()->count(), $oldCOunt+1);
        $response = $this->post('/dimension/enum/name/add', [
            'value' => 'Exist'
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_dim_name' => [
                "The value of the field for the new dimension name already exist in the data base"
            ]
        ]);
        $this->assertEquals(EnumDimensionName::all()->count(), $oldCOunt+1);
    }

    public function requiredForTest() {
        // Add the differents enum of the name if they didn't already exist in the database
        if (EnumDimensionName::all()->where('value', '=', 'Test')->count() === 0) {
            $countDimName=EnumDimensionName::all()->count();
            $response=$this->post('/dimension/enum/name/add', [
                'value' => 'Test',
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
        if (EnumEquipmentMassUnit::all()->where('value', '=', 'g')->count() === 0) {
            $countEqMassUnit=EnumEquipmentMassUnit::all()->count();
            $response=$this->post('/equipment/enum/massUnit/add', [
                'value' => 'g',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countEqMassUnit+1, EnumEquipmentMassUnit::all());
        }
        // Add the user to validate the equipment
        if (User::all()->where('user_firstName', '=', 'register')->count() === 0) {
            $countUser=User::all()->count();
            $response=$this->post('register', [
                'user_firstName' => 'dimTester',
                'user_lastName' => 'dimTester',
                'user_pseudo' => 'dimTester',
                'user_password' => 'dimTesterdimTester',
                'user_confirmation_password' => 'dimTesterdimTester',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countUser+1, User::all());
        }
    }

    /**
     * Test Conception Number: 3
     * Analyze the enum 'Test' and expecting the correct data
     * Name: TestAnalyze
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
            'eq_type' => 'internal',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'TestAnalyze',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'TestAnalyze',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'TestAnalyze')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $response=$this->post('/equipment/add', [
            'eq_validate' => 'validated',
            'eq_internalReference' => 'Testvalidated',
            'eq_externalReference' => 'Testvalidated',
            'eq_name' => 'Testvalidated',
            'eq_serialNumber' => 'Testvalidated',
            'eq_constructor' => 'Testvalidated',
            'eq_set' => 'Testvalidated',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'Testvalidated',
            'eq_mobility' => true,
            'eq_type' => 'internal',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+2, Equipment::all()->count());
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'TestAnalyze',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'TestAnalyze',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'TestAnalyze')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);
        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->where('user_firstName', '=', 'dimTester')->last()->id,
        ]);
        $response->assertStatus(200);

        $response=$this->post('/equipment/validation/'.Equipment::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->where('user_firstName', '=', 'dimTester')->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('dimension/enum/name/analyze/' . EnumDimensionName::all()->last()->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'equipments',
            'validated_eq',
        ]);
        $response->assertJson([
            'id' => EnumDimensionName::all()->last()->id,
            'equipments' => [
                '0' => [
                    "eqTemp_id" => 1,
                    "name" => "Test",
                    "internalReference" => "Test"
                ],
            ],
            'validated_eq' => [
                '0' => [
                    "eqTemp_id" => 2,
                    "name" => "Testvalidated",
                    "internalReference" => "Testvalidated"
                ]
            ],
        ]);
    }

    /**
     * Test Conception Number: 4
     * Try to update an enum linked to a drafted equipment with a non-existent name in the database
     * Name: Test1
     * Expected result: The name is correctly updated in the database
     * @returns void
     */
    public function test_update_enum_linked_to_drafted_with_non_existent_name() {
        $this->requiredForTest();
        $countEquipment = Equipment::all()->count();
        $response=$this->post('/equipment/add', [
            'eq_validate' => 'drafted',
            'eq_internalReference' => 'TestUpdateEnum',
            'eq_externalReference' => 'TestUpdateEnum',
            'eq_name' => 'TestUpdateEnum',
            'eq_serialNumber' => 'TestUpdateEnum',
            'eq_constructor' => 'TestUpdateEnum',
            'eq_set' => 'TestUpdateEnum',
            'eq_massUnit' => 'g',
            'eq_mass' => 12,
            'eq_remarks' => 'TestUpdateEnum',
            'eq_mobility' => true,
            'eq_type' => 'internal',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($countEquipment+1, Equipment::all()->count());
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'TestAnalyze',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'TestAnalyze',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->where('value', '=', 'External')->first()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->where('value', '=', 'TestAnalyze')->first()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->where('value', '=', 'mm')->first()->id,
            'equipmentTemp_id' => EquipmentTemp::all()->where('equipment_id', '=', Equipment::all()->last()->id)->first()->id,
            'dim_validate' => 'drafted'
        ]);

    }
}
