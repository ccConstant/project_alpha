<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Dimension;
use App\Models\EnumDimensionName;
use App\Models\EnumDimensionUnit;
use App\Models\EnumDimensionType;
use Illuminate\Database\Eloquent\Collection ;
use Laravel\Dusk\Browser;


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
    public function test_add_dim_drafted_addMenu_NoValue(){
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
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
    public function test_add_dim_drafted_addMenu_TooLongValue(){

        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_validate' => 'drafted',
            'dim_value'=> '123456789123456789123456789123456789123456789123456789'
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
    public function test_add_dim_drafted_addMenu_CorrectValue(){
        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_validate' => 'drafted',
            'dim_value'=> '47'
        ]);
        $response->assertStatus(200);
        $countDim=Dimension::all()->count();
        $response=$this->post('/equipment/add/dim', [
            'dim_validate' => 'drafted',
            'dim_value'=> '47',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => NULL,
            'enumDimensionName_id' => NULL,
            'dim_value' => 47,
            'enumDimensionUnit_id' => NULL,
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'dim_validate' => 'drafted'
        ]);
    }


     /**
     * Test Conception Number : 4
     * Saved successfully a dimension as drafted from add menu 		
     * Type : External  Name : Length  Value : 18 Unit : mm 
     * Expected result : The dimension is correctly saved in data base and correctly linked to the equipment 
     * @return void
     */
    public function test_add_dim_drafted_addMenu_CorrectValues(){
        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $countDimType=EnumDimensionType::all()->count();
        $response=$this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType+1, EnumDimensionType::all());

        $countDimName=EnumDimensionName::all()->count();
        $response=$this->post('/dimension/enum/name/add', [
            'value' => 'Length',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimName+1, EnumDimensionName::all());

        $countDimUnit=EnumDimensionUnit::all()->count();
        $response=$this->post('/dimension/enum/unit/add', [
            'value' => 'mm',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimUnit+1, EnumDimensionUnit::all());

        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'dim_validate' => 'drafted'
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id'=> 1
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id'=> 1
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id'=> 1
        ]);
    }





    /**
     * Test Conception Number : 5
     * Saved a dimension as to be validated from add menu with no value	
     * Type : /  Name : /  Value : / Unit : / 
     * Expected result : Receiving an error "You must enter a value for your dimension"
     * @return void
     */
    public function test_add_dim_toBeValidated_addMenu_NoValue(){
       
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
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
    public function test_add_dim_toBeValidated_addMenu_TooLongValue(){
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_validate' => 'to_be_validated',
            'dim_value'=> '123456789123456789123456789123456789123456789123456789'
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
    public function test_add_dim_toBeValidated_addMenu_CorrectValue(){
        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_validate' => 'to_be_validated',
            'dim_value'=> '8930'
        ]);
        $response->assertStatus(200);
        $countDim=Dimension::all()->count();
        $response=$this->post('/equipment/add/dim', [
            'dim_validate' => 'to_be_validated',
            'dim_value'=> '8930',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDim+1, Dimension::all());
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => NULL,
            'enumDimensionName_id' => NULL,
            'dim_value' => 8930,
            'dim_validate' => 'to_be_validated',
            'enumDimensionUnit_id' => NULL,
            'equipmentTemp_id' => Equipment::all()->last()->id,
        ]);
    }


     /**
     * Test Conception Number : 8
     * Saved successfully a dimension as to be validated from add menu 		
     * Type : External  Name : Length  Value : 18 Unit : mm 
     * Expected result : The dimension is correctly saved in data base and correctly linked to the equipment 
     * @return void
     */
    public function test_add_dim_toBeValidated_addMenu_CorrectValues(){
        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $countDimType=EnumDimensionType::all()->count();
        $response=$this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType+1, EnumDimensionType::all());

        $countDimName=EnumDimensionName::all()->count();
        $response=$this->post('/dimension/enum/name/add', [
            'value' => 'Length',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimName+1, EnumDimensionName::all());

        $countDimUnit=EnumDimensionUnit::all()->count();
        $response=$this->post('/dimension/enum/unit/add', [
            'value' => 'mm',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimUnit+1, EnumDimensionUnit::all());

        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'to_be_validated',
            'dim_value'=> '18',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'to_be_validated',
            'dim_value'=> '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'dim_validate' => 'to_be_validated',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id'=> EnumDimensionType::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id'=> EnumDimensionName::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id'=> EnumDimensionUnit::all()->last()->id
        ]);
    }

    /**
     * Test Conception Number : 9
     * Saved a dimension as validated from add menu with no value	
     * Type : /  Name : /  Value : / Unit : / 
     * Expected result : Receiving an error "You must enter a value for your dimension"
     * @return void
     */
    public function test_add_dim_validated_addMenu_NoValue(){
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_validate' => 'validated'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'dim_value' => 'You must enter a value for your dimension'
        ]);
    }

    /**
     * Test Conception Number : 10
     * Saved a dimension as validated from add menu with a too long value	
     * Type : /  Name : /  Value : "123456789123456789123456789123456789123456789123456789" Unit : / 
     * Expected result : Receiving an error "You must enter a maximum of 50 characters"
     * @return void
     */
    public function test_add_dim_validated_addMenu_TooLongValue(){
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value'=> '123456789123456789123456789123456789123456789123456789'
        ]);
        $response->assertStatus(302);
        $response->assertInvalid([
            'dim_value' => 'You must enter a maximum of 50 characters'
        ]);
    }

    /**
     * Test Conception Number : 11
     * Saved a dimension as validated  from add menu with a correct value but no type, no name and no unit	
     * Type : /  Name : /  Value : 32 Unit : / 
     * Expected result : Receiving errors "You must choose a type for your dimension", "You must choose a name for your dimension", "You must choose a unit for your dimension" 
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValueOnly(){
        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value'=> '32'
        ]);

        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_unit' => 'You must choose a unit for your dimension',
            'dim_type' => 'You must choose a type for your dimension',
            'dim_name' => 'You must choose a name for your dimension',
        ]);
    }

    /**
     * Test Conception Number : 12
     * Saved a dimension as validated  from add menu with a correct value but no type and no name	
     * Type : /  Name : /  Value : 18 Unit : mm
     * Expected result : Receiving errors "You must choose a type for your dimension", "You must choose a name for your dimension"
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValueUnit(){
        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $countDimUnit=EnumDimensionUnit::all()->count();
        $response=$this->post('/dimension/enum/unit/add', [
            'value' => 'mm',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimUnit+1, EnumDimensionUnit::all());

        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value'=> '18',
            'dim_unit' => 'mm'
        ]);

        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_type' => 'You must choose a type for your dimension',
            'dim_name' => 'You must choose a name for your dimension',
        ]);
    }

    /**
     * Test Conception Number : 13
     * Saved a dimension as validated  from add menu with a correct value but no type and no unit	
     * Type : /  Name : Length  Value : 18 Unit : /
     * Expected result : Receiving errors "You must choose a type for your dimension","You must choose a unit for your dimension" 
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValueName(){
        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $countDimName=EnumDimensionName::all()->count();
        $response=$this->post('/dimension/enum/name/add', [
            'value' => 'Length',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimName+1, EnumDimensionName::all());

        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value'=> '18',
            'dim_name' => 'Length'
        ]);

        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_type' => 'You must choose a type for your dimension',
            'dim_unit' => 'You must choose a unit for your dimension',
        ]);
    }

    /**
     * Test Conception Number : 14
     * Saved a dimension as validated  from add menu with a correct value but no type	
     * Type : /  Name : Length  Value : 18 Unit : mm
     * Expected result : Receiving errors "You must choose a type for your dimension", 
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValueNameUnit(){
        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $countDimName=EnumDimensionName::all()->count();
        $response=$this->post('/dimension/enum/name/add', [
            'value' => 'Length',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimName+1, EnumDimensionName::all());

        $countDimUnit=EnumDimensionUnit::all()->count();
        $response=$this->post('/dimension/enum/unit/add', [
            'value' => 'mm',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimUnit+1, EnumDimensionUnit::all());

        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value'=> '18',
            'dim_name' => 'Length',
            'dim_unit'  => 'mm'
        ]);

        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_type' => 'You must choose a type for your dimension',
        ]);
    }

    /**
     * Test Conception Number : 15
     * Saved a dimension as validated  from add menu with a correct value but no name and no unit	
     * Type : External  Name : /  Value : 18 Unit : /
     * Expected result : Receiving errors "You must choose a name for your dimension", "You must choose a unit for your dimension" 
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValueType(){
       
        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $countDimType=EnumDimensionType::all()->count();
        $response=$this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType+1, EnumDimensionType::all());

        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value'=> '18',
            'dim_type' => 'External',
        ]);

        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_name' => 'You must choose a name for your dimension',
            'dim_unit' => 'You must choose a unit for your dimension',
        ]);
    }

    /**
     * Test Conception Number : 16
     * Saved a dimension as validated  from add menu with a correct value but no name  	
     * Type : External  Name : /  Value : 18 Unit : mm
     * Expected result : Receiving errors "You must choose a name for your dimension"
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValueTypeUnit(){
        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $countDimType=EnumDimensionType::all()->count();
        $response=$this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType+1, EnumDimensionType::all());

        $countDimUnit=EnumDimensionUnit::all()->count();
        $response=$this->post('/dimension/enum/unit/add', [
            'value' => 'mm',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimUnit+1, EnumDimensionUnit::all());

        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value'=> '18',
            'dim_type' => 'External',
            'dim_unit'  => 'mm'
        ]);

        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_name' => 'You must choose a name for your dimension',
        ]);
    }

    /**
     * Test Conception Number : 17
     * Saved a dimension as validated  from add menu with a correct value but no unit	
     * Type : External  Name : Length  Value : 18 Unit : /
     * Expected result : Receiving errors "You must choose a unit for your dimension"
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValueTypeName(){
        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $countDimType=EnumDimensionType::all()->count();
        $response=$this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType+1, EnumDimensionType::all());

        $countDimName=EnumDimensionName::all()->count();
        $response=$this->post('/dimension/enum/name/add', [
            'value' => 'Length',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimName+1, EnumDimensionName::all());

        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_validate' => 'validated',
            'dim_value'=> '18',
            'dim_type' => 'External',
            'dim_name'  => 'Length'
        ]);

        $response->assertStatus(429);
        $response->assertInvalid([
            'dim_unit' => 'You must choose a unit for your dimension',
        ]);
    }

    /**
     * Test Conception Number : 18
     * Saved successfully a dimension as validated from add menu 		
     * Type : External  Name : Length  Value : 18 Unit : mm 
     * Expected result : The dimension is correctly saved in data base and correctly linked to the equipment 
     * @return void
     */
    public function test_add_dim_validated_addMenu_CorrectValues(){

        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $countDimType=EnumDimensionType::all()->count();
        $response=$this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType+1, EnumDimensionType::all());

        $countDimName=EnumDimensionName::all()->count();
        $response=$this->post('/dimension/enum/name/add', [
            'value' => 'Length',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimName+1, EnumDimensionName::all());

        $countDimUnit=EnumDimensionUnit::all()->count();
        $response=$this->post('/dimension/enum/unit/add', [
            'value' => 'mm',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimUnit+1, EnumDimensionUnit::all());

        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value'=> '18',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'validated',
            'dim_value'=> '18',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'dim_validate' => 'validated',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id'=> EnumDimensionType::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id'=> EnumDimensionName::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id'=> EnumDimensionUnit::all()->last()->id
        ]);
    }

    /**
     * Test Conception Number : 19
     * Create a new equipment with the set "Set1" and import the dimension of the equipment with the same set			
     * Type : N/A  Name : N/A  Value : N/A Unit : N/A
     * Expected result : The dimension is correctly imported with the right data (type : external, name : length, value : 29, unit : mm) 
     * Dimension Data for all import tests: it's necessary to create an equipment with a dimension with theses values for the following test	
     * Type : External  Name : Length  Value : 29 Unit : mm		
     * Eq External Ref / Eq Internal Ref : Example1 
     * Eq set : Set1 
     * @return void
     */

    public function test_import_dim(){
    }

    /**
     * Test Conception Number : 20
     * Update successfully a dimension as drafted with only a value	
     * Type : /  Name : /  Value : 47 Unit : /
     * Expected result : The dimension is correctly updated in data base 
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests			
     * Type : External  Name : Length  Value : 29 Unit : mm		
     * Eq External Ref / Eq Internal Ref : Example1 
     * Eq set : /
     * @return void
     */

    public function test_update_dim_draft_correctValue(){
        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $countDimType=EnumDimensionType::all()->count();
        $response=$this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType+1, EnumDimensionType::all());

        $countDimName=EnumDimensionName::all()->count();
        $response=$this->post('/dimension/enum/name/add', [
            'value' => 'Length',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimName+1, EnumDimensionName::all());

        $countDimUnit=EnumDimensionUnit::all()->count();
        $response=$this->post('/dimension/enum/unit/add', [
            'value' => 'mm',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimUnit+1, EnumDimensionUnit::all());

        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value'=> '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value'=> '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'dim_validate' => 'drafted',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id'=> EnumDimensionType::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id'=> EnumDimensionName::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id'=> EnumDimensionUnit::all()->last()->id
        ]);
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value'=> '47',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $url='/equipment/update/dim/'.Dimension::all()->last()->id;
        $response=$this->post($url, [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value'=> '47',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => 47,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'dim_validate' => 'drafted',
        ]);

    }

    /**
     * Test Conception Number : 21
     * Update successfully a dimension as drafted with a type, name value and a unit	
     * Type : Internal  Name : Width  Value : 18 Unit : cm
     * Expected result : The dimension is correctly updated in data base 
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests			
     * Type : External  Name : Length  Value : 29 Unit : mm		
     * Eq External Ref / Eq Internal Ref : Example1 
     * Eq set : /
     * @return void
     */

    public function test_update_dim_drafted_correctValues(){
        
        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $countDimType=EnumDimensionType::all()->count();
        $response=$this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $response=$this->post('/dimension/enum/type/add', [
            'value' => 'Internal',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType+2, EnumDimensionType::all());

        $countDimName=EnumDimensionName::all()->count();
        $response=$this->post('/dimension/enum/name/add', [
            'value' => 'Length',
        ]);
        $response->assertStatus(200);
        $response=$this->post('/dimension/enum/name/add', [
            'value' => 'Width',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimName+2, EnumDimensionName::all());

        $countDimUnit=EnumDimensionUnit::all()->count();
        $response=$this->post('/dimension/enum/unit/add', [
            'value' => 'mm',
        ]);
        $response->assertStatus(200);
        $response=$this->post('/dimension/enum/unit/add', [
            'value' => 'cm',
        ]);
        $this->assertCount($countDimUnit+2, EnumDimensionUnit::all());

        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value'=> '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value'=> '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id-1,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id-1,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id-1,
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'dim_validate' => 'drafted',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id'=> EnumDimensionType::all()->last()->id-1
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'Internal',
            'id'=> EnumDimensionType::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id'=> EnumDimensionName::all()->last()->id-1
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Width',
            'id'=> EnumDimensionName::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id'=> EnumDimensionUnit::all()->last()->id-1
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'cm',
            'id'=> EnumDimensionUnit::all()->last()->id
        ]);
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'Internal',
            'dim_name' => 'Width',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'cm'
        ]);
        $response->assertStatus(200);
        $url='/equipment/update/dim/'.Dimension::all()->last()->id;
        $response=$this->post($url, [
            'dim_type' => 'Internal',
            'dim_name' => 'Width',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'cm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'dim_validate' => 'drafted',
        ]);

    }

    /**
     * Test Conception Number : 22
     * Update successfully a dimension as to be validated with only a value	
     * Type : /  Name : /  Value : 8930 Unit : /
     * Expected result : The dimension is correctly updated in data base 
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests			
     * Type : External  Name : Length  Value : 29 Unit : mm		
     * Eq External Ref / Eq Internal Ref : Example1 
     * Eq set : /
     * @return void
     */

     public function test_update_dim_toBeValidated_correctValue(){
        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $countDimType=EnumDimensionType::all()->count();
        $response=$this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType+1, EnumDimensionType::all());

        $countDimName=EnumDimensionName::all()->count();
        $response=$this->post('/dimension/enum/name/add', [
            'value' => 'Length',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimName+1, EnumDimensionName::all());

        $countDimUnit=EnumDimensionUnit::all()->count();
        $response=$this->post('/dimension/enum/unit/add', [
            'value' => 'mm',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimUnit+1, EnumDimensionUnit::all());

        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'to_be_validated',
            'dim_value'=> '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value'=> '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'dim_validate' => 'drafted',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id'=> EnumDimensionType::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id'=> EnumDimensionName::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id'=> EnumDimensionUnit::all()->last()->id
        ]);
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'to_be_validated',
            'dim_value'=> '8930',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $url='/equipment/update/dim/'.Dimension::all()->last()->id;
        $response=$this->post($url, [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'to_be_validated',
            'dim_value'=> '8930',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => 8930,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'dim_validate' => 'to_be_validated',
        ]);

    }

    /**
     * Test Conception Number : 21
     * Update successfully a dimension as drafted with a type, name value and a unit	
     * Type : Internal  Name : Width  Value : 18 Unit : cm
     * Expected result : The dimension is correctly updated in data base 
     * Dimension Data for all update tests: it's necessary to create a dimension with theses values for all following tests			
     * Type : External  Name : Length  Value : 29 Unit : mm		
     * Eq External Ref / Eq Internal Ref : Example1 
     * Eq set : /
     * @return void
     */

    public function test_update_dim_toBeValidated_correctValues(){
        
        $countEq=Equipment::all()->count();
        $response=$this->post('/equipment/verif', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add', [
            'eq_internalReference' => 'Test',
            'eq_externalReference' => 'Test',
            'reason' => 'add',
            'eq_validate' => 'drafted'

        ]);
        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $response->assertStatus(200);
        $this->assertCount($countEq+1, Equipment::all());

        $countDimType=EnumDimensionType::all()->count();
        $response=$this->post('/dimension/enum/type/add', [
            'value' => 'External',
        ]);
        $response->assertStatus(200);
        $response=$this->post('/dimension/enum/type/add', [
            'value' => 'Internal',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimType+2, EnumDimensionType::all());

        $countDimName=EnumDimensionName::all()->count();
        $response=$this->post('/dimension/enum/name/add', [
            'value' => 'Length',
        ]);
        $response->assertStatus(200);
        $response=$this->post('/dimension/enum/name/add', [
            'value' => 'Width',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countDimName+2, EnumDimensionName::all());

        $countDimUnit=EnumDimensionUnit::all()->count();
        $response=$this->post('/dimension/enum/unit/add', [
            'value' => 'mm',
        ]);
        $response->assertStatus(200);
        $response=$this->post('/dimension/enum/unit/add', [
            'value' => 'cm',
        ]);
        $this->assertCount($countDimUnit+2, EnumDimensionUnit::all());

        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value'=> '29',
            'dim_unit' => 'mm'
        ]);
        $response->assertStatus(200);
        $response=$this->post('/equipment/add/dim', [
            'dim_type' => 'External',
            'dim_name' => 'Length',
            'dim_validate' => 'drafted',
            'dim_value'=> '29',
            'dim_unit' => 'mm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id-1,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id-1,
            'dim_value' => 29,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id-1,
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'dim_validate' => 'drafted',
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'External',
            'id'=> EnumDimensionType::all()->last()->id-1
        ]);
        $this->assertDatabaseHas('enum_dimension_types', [
            'value' => 'Internal',
            'id'=> EnumDimensionType::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Length',
            'id'=> EnumDimensionName::all()->last()->id-1
        ]);
        $this->assertDatabaseHas('enum_dimension_names', [
            'value' => 'Width',
            'id'=> EnumDimensionName::all()->last()->id
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'mm',
            'id'=> EnumDimensionUnit::all()->last()->id-1
        ]);
        $this->assertDatabaseHas('enum_dimension_units', [
            'value' => 'cm',
            'id'=> EnumDimensionUnit::all()->last()->id
        ]);
        $countDim=Dimension::all()->count();
        $response=$this->post('/dimension/verif', [
            'dim_type' => 'Internal',
            'dim_name' => 'Width',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'cm'
        ]);
        $response->assertStatus(200);
        $url='/equipment/update/dim/'.Dimension::all()->last()->id;
        $response=$this->post($url, [
            'dim_type' => 'Internal',
            'dim_name' => 'Width',
            'dim_validate' => 'drafted',
            'dim_value'=> '18',
            'dim_unit' => 'cm',
            'eq_id' => Equipment::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dimensions', [
            'enumDimensionType_id' => EnumDimensionType::all()->last()->id,
            'enumDimensionName_id' => EnumDimensionName::all()->last()->id,
            'dim_value' => 18,
            'enumDimensionUnit_id' => EnumDimensionUnit::all()->last()->id,
            'equipmentTemp_id' => Equipment::all()->last()->id,
            'dim_validate' => 'drafted',
        ]);

    }

   

    










    

}

