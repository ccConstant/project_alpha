<?php

namespace Tests\Feature;

use App\Models\SW01\EnumUsageMetrologicalLevel;
use App\Models\SW01\EnumVerifAcceptanceAuthority;
use App\Models\SW01\EnumVerificationRequiredSkill;
use App\Models\SW01\Mme;
use App\Models\SW01\MmeTemp;
use App\Models\SW01\MmeUsage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnumMetrologicalLevelTest extends TestCase
{
    use RefreshDatabase;

    public function create_user($name) {
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

    public function create_mme($name, $validated = 'drafted')
    {
        $user_id = $this->create_user('test');
        if (EnumVerificationRequiredSkill::all()->count() == 0) {
            $response = $this->post('/verification/enum/requiredSkill/add', [
                'value' => 'Skill',
            ]);
            $response->assertStatus(200);
        }
        if (EnumVerifAcceptanceAuthority::all()->count() == 0) {
            $response = $this->post('/verification/enum/verifAcceptanceAuthority/add', [
                'value' => 'Authority',
            ]);
            $response->assertStatus(200);
        }

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
     * Test Conception Number: 1
     * Try to add a non-existent name in the database
     * Type: Type
     * Expected result: The name is correctly added to the database
     * @return void
     */
    public function test_add_non_existent_name()
    {
        $oldCOunt = EnumUsageMetrologicalLevel::all()->count();
        $response = $this->post('/usage/enum/metrologicalLevel/add', [
            'value' => 'Type'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(EnumUsageMetrologicalLevel::all()->count(), $oldCOunt + 1);
    }

    /**
     * Test Conception Number: 2
     * Try to add two time the same name in the database
     * Type : Exist
     * Expected result: Receiving an error:
     *                                      "The value of the field for the new usage metrological level already exist in the data base"
     * @return void
     */
    public function test_add_two_time_same_name()
    {
        $oldCOunt = EnumUsageMetrologicalLevel::all()->count();
        $response = $this->post('/usage/enum/metrologicalLevel/add', [
            'value' => 'Exist'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(EnumUsageMetrologicalLevel::all()->count(), $oldCOunt + 1);
        $response = $this->post('/usage/enum/metrologicalLevel/add', [
            'value' => 'Exist'
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_metrologicalLevel' => [
                "The value of the field for the new usage metrological level already exist in the data base"
            ]
        ]);
        $this->assertEquals(EnumUsageMetrologicalLevel::all()->count(), $oldCOunt + 1);
    }

    /**
     * Test Conception Number: 3
     * Analyze the enum 'Type' and expecting the correct data
     * Type: Type
     * Expected result: The data contain one validated mme and one not validated
     * @returns void
     */
    public function test_analyze_data()
    {
        $this->requiredForTest();

        $countPrec = MmeUsage::all()->count();
        $response = $this->post('/mme_usage/verif', [
            'usg_metrologicalLevel' => 'Type',
            'usg_measurementType' => 'TestAnalyze',
            'usg_precision' => 'TestAnalyze',
            'usg_validate' => 'drafted',
            'usg_application' => 'TestAnalyze',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/usg', [
            'usg_metrologicalLevel' => 'Type',
            'usg_measurementType' => 'TestAnalyze',
            'usg_precision' => 'TestAnalyze',
            'usg_validate' => 'drafted',
            'usg_application' => 'TestAnalyze',
            'user_id' => User::all()->last()->id,
            'mme_id' => Mme::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countPrec + 1, MmeUsage::all());
        $this->assertDatabaseHas('mme_usages', [
            'enumUsageMetrologicalLevel_id' => EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id,
            'usg_measurementType' => 'TestAnalyze',
            'usg_precision' => 'TestAnalyze',
            'usg_validate' => 'drafted',
            'mmeTemp_id' => MmeTemp::all()->last()->id,
        ]);

        $mme_id = $this->create_mme('Testvalidated', 'validated');

        $countPrec = MmeUsage::all()->count();
        $response = $this->post('/mme_usage/verif', [
            'usg_metrologicalLevel' => 'Type',
            'usg_measurementType' => 'Type',
            'usg_precision' => 'TestAnalyze',
            'usg_validate' => 'drafted',
            'usg_application' => 'TestAnalyze',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/usg', [
            'usg_metrologicalLevel' => 'Type',
            'usg_measurementType' => 'TestAnalyze',
            'usg_precision' => 'TestAnalyze',
            'usg_validate' => 'drafted',
            'usg_application' => 'TestAnalyze',
            'user_id' => User::all()->last()->id,
            'mme_id' => Mme::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countPrec + 1, MmeUsage::all());
        $this->assertDatabaseHas('mme_usages', [
            'enumUsageMetrologicalLevel_id' => EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id,
            'usg_validate' => 'drafted',
            'mmeTemp_id' => MmeTemp::all()->last()->id,
        ]);
        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/usage/enum/metrologicalLevel/analyze/' . EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'mmes',
            'validated_mme',
        ]);
        $response->assertJson([
            'id' => EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id,
            'mmes' => [
                '0' => [
                    "mmeTemp_id" => MmeTemp::all()->where('mme_id', '=', Mme::all()->where('mme_internalReference', '=', 'Test')->last()->id)->first()->id,
                    "name" => "Test",
                    "internalReference" => "Test"
                ],
            ],
            'validated_mme' => [
                '0' => [
                    "mmeTemp_id" => MmeTemp::all()->where('mme_id', '=', Mme::all()->where('mme_internalReference', '=', 'Testvalidated')->first()->id)->first()->id,
                    "name" => "Testvalidated",
                    "internalReference" => "Testvalidated"
                ]
            ],
        ]);
    }

    public function requiredForTest()
    {
        $user_id = $this->create_user('test');
        $mme_id = $this->create_mme('Test', 'validated');
        // Add the different enum of the precaution if they didn't already exist in the database
        if (EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->count() === 0) {
            $countPrecValues = EnumUsageMetrologicalLevel::all()->count();
            $response = $this->post('/usage/enum/metrologicalLevel/add', [
                'value' => 'Type',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countPrecValues + 1, EnumUsageMetrologicalLevel::all());
        }
        if (EnumUsageMetrologicalLevel::all()->where('value', '=', 'Exist')->count() === 0) {
            $countPrecValues = EnumUsageMetrologicalLevel::all()->count();
            $response = $this->post('/usage/enum/metrologicalLevel/add', [
                'value' => 'Exist',
            ]);
            $response->assertStatus(200);
            $this->assertCount($countPrecValues + 1, EnumUsageMetrologicalLevel::all());
        }
    }

    /**
     * Test Conception Number: 4
     * Try to update an enum linked to drafted mme with a non-existent name in the database
     * Type: TestDrafted
     * Expected result: The name is correctly updated in the database
     * @returns void
     */
    public function test_update_enum_linked_to_drafted_with_non_existent_name()
    {
        $this->requiredForTest();
        $countPrec = MmeUsage::all()->count();
        $response = $this->post('/mme_usage/verif', [
            'usg_metrologicalLevel' => 'Type',
            'usg_measurementType' => 'Type',
            'usg_precision' => 'TestAnalyze',
            'usg_validate' => 'drafted',
            'usg_application' => 'TestAnalyze',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/usg', [
            'usg_metrologicalLevel' => 'Type',
            'usg_measurementType' => 'TestAnalyze',
            'usg_precision' => 'TestAnalyze',
            'usg_validate' => 'drafted',
            'usg_application' => 'TestAnalyze',
            'user_id' => User::all()->last()->id,
            'mme_id' => Mme::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countPrec + 1, MmeUsage::all());
        $this->assertDatabaseHas('mme_usages', [
            'enumUsageMetrologicalLevel_id' => EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id,
            'usg_validate' => 'drafted',
            'mmeTemp_id' => MmeTemp::all()->last()->id,
        ]);
        $oldId = EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id;
        $response = $this->post('/usage/enum/metrologicalLevel/verif/' . EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id, [
            'value' => 'TestDrafted'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/usage/enum/metrologicalLevel/update/' . EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id, [
            'value' => 'TestDrafted',
            'validated_mme' => []
        ]);
        $response->assertStatus(200);
        $newId = EnumUsageMetrologicalLevel::all()->where('value', '=', 'TestDrafted')->first()->id;
        $this->assertEquals($oldId, $newId);
        $this->assertDatabaseHas('enum_usage_metrological_levels', [
            'value' => 'TestDrafted',
        ]);
        $this->assertDatabaseHas('mme_usages', [
            'enumUsageMetrologicalLevel_id' => EnumUsageMetrologicalLevel::all()->where('value', '=', 'TestDrafted')->first()->id,
            'usg_validate' => 'drafted',
            'mmeTemp_id' => MmeTemp::all()->last()->id,
        ]);
    }

    /**
     * Test Conception Number: 5
     * Try to update an enum linked to to_be_validated mme with a non-existent name in the database
     * Type: TestToBeValidated
     * Expected result: The name is correctly updated in the database
     * @returns void
     */
    public function test_update_enum_linked_to_toBeValidated_with_non_existent_name()
    {
        $this->requiredForTest();
        $countPrec = MmeUsage::all()->count();
        $response = $this->post('/mme_usage/verif', [
            'usg_metrologicalLevel' => 'Type',
            'usg_measurementType' => 'Type',
            'usg_precision' => 'TestAnalyze',
            'usg_validate' => 'drafted',
            'usg_application' => 'TestAnalyze',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/usg', [
            'usg_metrologicalLevel' => 'Type',
            'usg_measurementType' => 'TestAnalyze',
            'usg_precision' => 'TestAnalyze',
            'usg_validate' => 'drafted',
            'usg_application' => 'TestAnalyze',
            'user_id' => User::all()->last()->id,
            'mme_id' => Mme::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countPrec + 1, MmeUsage::all());
        $this->assertDatabaseHas('mme_usages', [
            'enumUsageMetrologicalLevel_id' => EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id,
            'usg_validate' => 'drafted',
            'mmeTemp_id' => MmeTemp::all()->last()->id,
        ]);
        $response = $this->post('/usage/enum/metrologicalLevel/verif/' . EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id, [
            'value' => 'TestToBeValidated'
        ]);
        $response->assertStatus(200);
        $oldId = EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id;
        $response = $this->post('/usage/enum/metrologicalLevel/update/' . EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id, [
            'value' => 'TestToBeValidated',
            'validated_mme' => []
        ]);
        $response->assertStatus(200);
        $newId = EnumUsageMetrologicalLevel::all()->where('value', '=', 'TestToBeValidated')->first()->id;
        $this->assertEquals($oldId, $newId);
        $this->assertDatabaseHas('enum_usage_metrological_levels', [
            'value' => 'TestToBeValidated',
        ]);
        $this->assertDatabaseHas('mme_usages', [
            'enumUsageMetrologicalLevel_id' => EnumUsageMetrologicalLevel::all()->where('value', '=', 'TestToBeValidated')->first()->id,
            'usg_validate' => 'drafted',
            'mmeTemp_id' => MmeTemp::all()->last()->id,
        ]);
    }

    /**
     * Test Conception Number: 6
     * Try to update an enum linked to validated mme with a non-existent name in the database
     * Type: TestValidated
     * Expected result: The name is correctly updated in the database, and a history is created in the database
     * @returns void
     */
    public function test_update_enum_linked_to_validated_with_non_existent_name()
    {
        $this->requiredForTest();
        $countPrec = MmeUsage::all()->count();
        $response = $this->post('/mme_usage/verif', [
            'usg_metrologicalLevel' => 'Type',
            'usg_measurementType' => 'Type',
            'usg_precision' => 'TestAnalyze',
            'usg_validate' => 'drafted',
            'usg_application' => 'TestAnalyze',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/usg', [
            'usg_metrologicalLevel' => 'Type',
            'usg_measurementType' => 'TestAnalyze',
            'usg_precision' => 'TestAnalyze',
            'usg_validate' => 'drafted',
            'usg_application' => 'TestAnalyze',
            'user_id' => User::all()->last()->id,
            'mme_id' => Mme::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countPrec + 1, MmeUsage::all());
        $this->assertDatabaseHas('mme_usages', [
            'enumUsageMetrologicalLevel_id' => EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id,
            'usg_validate' => 'drafted',
            'mmeTemp_id' => MmeTemp::all()->last()->id,
        ]);
        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->last()->id,
            'mmeTemp_validate' => 'validated',
            'mmeTemp_version' => 1,
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id,
        ]);
        $oldId = EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id;
        $response = $this->post('/usage/enum/metrologicalLevel/analyze/' . EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id);
        $response->assertStatus(200);
        $tab = array();
        foreach (json_decode($response->getContent())->validated_mme as $mme) {
            array_push($tab, array(
                'mmeTemp_id' => $mme->mmeTemp_id,
                'name' => $mme->name,
                'internalReference' => $mme->internalReference,
            ));
        }
        $response = $this->post('/usage/enum/metrologicalLevel/verif/' . EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id, [
            'value' => 'TestValidated'
        ]);
        $response->assertStatus(200);
        $response = $this->post('/usage/enum/metrologicalLevel/update/' . EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id, [
            'value' => 'TestValidated',
            'validated_mme' => $tab,
            'history_reasonUpdate' => 'TestUpdateEnum3',
        ]);
        $response->assertStatus(200);
        $this->assertCount($countPrec + 1, MmeUsage::all());
        $newId = EnumUsageMetrologicalLevel::all()->where('value', '=', 'TestValidated')->first()->id;
        $this->assertEquals($oldId, $newId);
        $this->assertDatabaseHas('enum_usage_metrological_levels', [
            'value' => 'TestValidated',
        ]);
        $this->assertDatabaseHas('mme_usages', [
            'enumUsageMetrologicalLevel_id' => EnumUsageMetrologicalLevel::all()->where('value', '=', 'TestValidated')->first()->id,
            'usg_validate' => 'drafted',
            'mmeTemp_id' => MmeTemp::all()->last()->id,
        ]);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->last()->id,
            'mmeTemp_validate' => 'validated',
            'mmeTemp_version' => 2,
            'qualityVerifier_id' => null,
            'technicalVerifier_id' => null,
        ]);
    }

    /**
     * Test Conception Number: 7
     * Try to update an enum linked to mme with an existent name in the database
     * Type: /
     * Expected result: Receiving an error:
     *                                      "The value of the field for the usage metrological level already exist in the data base"
     * @returns void
     */
    public function test_update_enum_with_existant_value()
    {
        $this->requiredForTest();
        $countPrec = MmeUsage::all()->count();
        $response = $this->post('/mme_usage/verif', [
            'usg_metrologicalLevel' => 'Type',
            'usg_measurementType' => 'Type',
            'usg_precision' => 'TestAnalyze',
            'usg_validate' => 'drafted',
            'usg_application' => 'TestAnalyze',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/usg', [
            'usg_metrologicalLevel' => 'Type',
            'usg_measurementType' => 'TestAnalyze',
            'usg_precision' => 'TestAnalyze',
            'usg_validate' => 'drafted',
            'usg_application' => 'TestAnalyze',
            'user_id' => User::all()->last()->id,
            'mme_id' => Mme::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countPrec + 1, MmeUsage::all());
        $this->assertDatabaseHas('mme_usages', [
            'enumUsageMetrologicalLevel_id' => EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id,
            'usg_validate' => 'drafted',
            'mmeTemp_id' => MmeTemp::all()->last()->id,
        ]);
        $response = $this->post('/usage/enum/metrologicalLevel/verif/' . EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id, [
            'value' => 'Exist'
        ]);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_metrologicalLevel' => 'The value of the field for the usage metrological level already exist in the data base'
        ]);
    }

    /**
     * Test Conception Number: 8
     * Try to delete an enum not linked to an mme
     * Type: /
     * Expected result: The name is correctly deleted in the database
     * @returns void
     */
    public function test_delete_enum_not_linked()
    {
        $this->requiredForTest();
        $countEnumDimType = EnumUsageMetrologicalLevel::all()->count();
        $response = $this->post('/usage/enum/metrologicalLevel/delete/' . EnumUsageMetrologicalLevel::all()->where('value', '=', 'Exist')->first()->id);
        $response->assertStatus(200);
        $this->assertCount($countEnumDimType - 1, EnumUsageMetrologicalLevel::all());
    }

    /**
     * Test Conception Number: 9
     * Try to delete an enum linked to an mme
     * Type: TestValidated
     * Expected result: Receiving an error:
     *                                      "This value is already used in the data base so you can't delete it"
     * @returns void
     */
    public function test_delete_enum_linked()
    {
        $this->requiredForTest();
        $countPrec = MmeUsage::all()->count();
        $response = $this->post('/mme_usage/verif', [
            'usg_metrologicalLevel' => 'Type',
            'usg_measurementType' => 'Type',
            'usg_precision' => 'TestAnalyze',
            'usg_validate' => 'drafted',
            'usg_application' => 'TestAnalyze',
            'user_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $response = $this->post('/mme/add/usg', [
            'usg_metrologicalLevel' => 'Type',
            'usg_measurementType' => 'TestAnalyze',
            'usg_precision' => 'TestAnalyze',
            'usg_validate' => 'drafted',
            'usg_application' => 'TestAnalyze',
            'user_id' => User::all()->last()->id,
            'mme_id' => Mme::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertCount($countPrec + 1, MmeUsage::all());
        $this->assertDatabaseHas('mme_usages', [
            'enumUsageMetrologicalLevel_id' => EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id,
            'usg_validate' => 'drafted',
            'mmeTemp_id' => MmeTemp::all()->last()->id,
        ]);
        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'technical',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/mme/validation/' . Mme::all()->last()->id, [
            'reason' => 'quality',
            'enteredBy_id' => User::all()->last()->id,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('mme_temps', [
            'mme_id' => Mme::all()->last()->id,
            'mmeTemp_validate' => 'validated',
            'mmeTemp_version' => 1,
            'qualityVerifier_id' => User::all()->last()->id,
            'technicalVerifier_id' => User::all()->last()->id,
        ]);
        $response = $this->post('/usage/enum/metrologicalLevel/delete/' . EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id);
        $response->assertStatus(429);
        $response->assertInvalid([
            'enum_metrologicalLevel' => 'This value is already used in the data base so you can\'t delete it'
        ]);
        $this->assertDatabaseHas('enum_usage_metrological_levels', [
            'value' => 'Type',
        ]);
        $this->assertDatabaseHas('mme_usages', [
            'enumUsageMetrologicalLevel_id' => EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id,
            'usg_validate' => 'drafted',
            'mmeTemp_id' => MmeTemp::all()->last()->id,
        ]);
    }

    /**
     * Test Conception Number: 10
     * Try to consult the enum list
     * Type: TestValidated
     * Expected result: The enum list is correct, and we receive all the data
     * @returns void
     */
    public function test_consult_enum()
    {
        $this->requiredForTest();
        $response = $this->get('/usage/enum/metrologicalLevel');
        $response->assertJson([
            0 => [
                'id' => EnumUsageMetrologicalLevel::all()->where('value', '=', 'Exist')->first()->id,
                'value' => 'Exist',
                'id_enum' => 'MetrologicalLevel'
            ],
            1 => [
                'id' => EnumUsageMetrologicalLevel::all()->where('value', '=', 'Type')->first()->id,
                'value' => 'Type',
                'id_enum' => 'MetrologicalLevel'
            ],
        ]);
    }
}
