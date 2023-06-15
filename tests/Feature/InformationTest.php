<?php

/**
 * Filename: InformationTest.php
 * Creation date: 20 Apr 2023
 * Update date: 20 Apr 2023
 * This file contains all the tests about the information table.
 * Coverage : 100%
 */

namespace Tests\Feature;

use App\Models\Information;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InformationTest extends TestCase
{
    use RefreshDatabase;

    private function add_all_required_information()
    {
        $categories = [
            'eqIdCard',
            'file',
            'power',
            'dimension',
            'risk',
            'specialProcess',
            'state',
            'preventiveMaintenanceOperation',
            'curativeMaintenanceOperation',
            'preventiveMaintenanceOperationRealized',
            'usage',
            'user',
            'mme',
            'mme_state',
            'verif',
            'verifRlz',
            'mme_usage',
            'mme_precaution',
            'enum_test_'
        ];

        foreach ($categories as $category)
        {
            Information::create([
                'info_name' => $category.'1',
                'info_value' => $category.'1',
                'info_set' => $category
            ]);
            Information::create([
                'info_name' => $category.'2',
                'info_value' => $category.'2',
                'info_set' => $category
            ]);
        }
    }

    /**
     * Test Conception Number: 1
     * Consult the eqIdCard information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_eqIdCard_information()
    {
        $this->assertEquals(Information::all()->count(), 0);
        $this->add_all_required_information();
        $response = $this->get('/info/send/eqIdCard');
        $response->assertJson([
            '0' => [
                'info_name' => 'eqIdCard1',
                'info_value' =>'eqIdCard1',
                'info_set' => 'eqIdCard'
            ],
            '1' => [
                'info_name' => 'eqIdCard2',
                'info_value' =>'eqIdCard2',
                'info_set' => 'eqIdCard'
            ]
        ]);
    }

    /**
     * Test Conception Number: 2
     * Consult the file information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_file_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/file');
        $response->assertJson([
            '0' => [
                'info_name' => 'file1',
                'info_value' =>'file1',
                'info_set' => 'file'
            ],
            '1' => [
                'info_name' => 'file2',
                'info_value' =>'file2',
                'info_set' => 'file'
            ]
        ]);
    }

    /**
     * Test Conception Number: 3
     * Consult the power information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_power_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/power');
        $response->assertJson([
            '0' => [
                'info_name' => 'power1',
                'info_value' =>'power1',
                'info_set' => 'power'
            ],
            '1' => [
                'info_name' => 'power2',
                'info_value' =>'power2',
                'info_set' => 'power'
            ]
        ]);
    }

    /**
     * Test Conception Number: 4
     * Consult the dimension information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_dimension_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/dimension');
        $response->assertJson([
            '0' => [
                'info_name' => 'dimension1',
                'info_value' =>'dimension1',
                'info_set' => 'dimension'
            ],
            '1' => [
                'info_name' => 'dimension2',
                'info_value' =>'dimension2',
                'info_set' => 'dimension'
            ]
        ]);
    }

    /**
     * Test Conception Number: 5
     * Consult the risk information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_risk_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/risk');
        $response->assertJson([
            '0' => [
                'info_name' => 'risk1',
                'info_value' =>'risk1',
                'info_set' => 'risk'
            ],
            '1' => [
                'info_name' => 'risk2',
                'info_value' =>'risk2',
                'info_set' => 'risk'
            ]
        ]);
    }

    /**
     * Test Conception Number: 6
     * Consult the specialProcess information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_specialProcess_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/specialProcess');
        $response->assertJson([
            '0' => [
                'info_name' => 'specialProcess1',
                'info_value' =>'specialProcess1',
                'info_set' => 'specialProcess'
            ],
            '1' => [
                'info_name' => 'specialProcess2',
                'info_value' =>'specialProcess2',
                'info_set' => 'specialProcess'
            ]
        ]);
    }

    /**
     * Test Conception Number: 7
     * Consult the state information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_state_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/state');
        $response->assertJson([
            '0' => [
                'info_name' => 'state1',
                'info_value' =>'state1',
                'info_set' => 'state'
            ],
            '1' => [
                'info_name' => 'state2',
                'info_value' =>'state2',
                'info_set' => 'state'
            ]
        ]);
    }

    /**
     * Test Conception Number: 8
     * Consult the preventiveMaintenanceOperation information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_preventiveMaintenanceOperation_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/preventiveMaintenanceOperation');
        $response->assertJson([
            '0' => [
                'info_name' => 'preventiveMaintenanceOperation1',
                'info_value' =>'preventiveMaintenanceOperation1',
                'info_set' => 'preventiveMaintenanceOperation'
            ],
            '1' => [
                'info_name' => 'preventiveMaintenanceOperation2',
                'info_value' =>'preventiveMaintenanceOperation2',
                'info_set' => 'preventiveMaintenanceOperation'
            ]
        ]);
    }

    /**
     * Test Conception Number: 9
     * Consult the curativeMaintenanceOperation information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_curativeMaintenanceOperation_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/curativeMaintenanceOperation');
        $response->assertJson([
            '0' => [
                'info_name' => 'curativeMaintenanceOperation1',
                'info_value' =>'curativeMaintenanceOperation1',
                'info_set' => 'curativeMaintenanceOperation'
            ],
            '1' => [
                'info_name' => 'curativeMaintenanceOperation2',
                'info_value' =>'curativeMaintenanceOperation2',
                'info_set' => 'curativeMaintenanceOperation'
            ]
        ]);
    }

    /**
     * Test Conception Number: 10
     * Consult the preventiveMaintenanceOperationRealized information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_preventiveMaintenanceOperationRealized_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/preventiveMaintenanceOperationRealized');
        $response->assertJson([
            '0' => [
                'info_name' => 'preventiveMaintenanceOperationRealized1',
                'info_value' =>'preventiveMaintenanceOperationRealized1',
                'info_set' => 'preventiveMaintenanceOperationRealized'
            ],
            '1' => [
                'info_name' => 'preventiveMaintenanceOperationRealized2',
                'info_value' =>'preventiveMaintenanceOperationRealized2',
                'info_set' => 'preventiveMaintenanceOperationRealized'
            ]
        ]);
    }

    /**
     * Test Conception Number: 11
     * Consult the usage information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_usage_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/usage');
        $response->assertJson([
            '0' => [
                'info_name' => 'usage1',
                'info_value' =>'usage1',
                'info_set' => 'usage'
            ],
            '1' => [
                'info_name' => 'usage2',
                'info_value' =>'usage2',
                'info_set' => 'usage'
            ]
        ]);
    }

    /**
     * Test Conception Number: 12
     * Consult the user information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_user_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/person');
        $response->assertJson([
            '0' => [
                'info_name' => 'user1',
                'info_value' =>'user1',
                'info_set' => 'user'
            ],
            '1' => [
                'info_name' => 'user2',
                'info_value' =>'user2',
                'info_set' => 'user'
            ]
        ]);
    }

    /**
     * Test Conception Number: 13
     * Consult the mme information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_mme_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/mme');
        $response->assertJson([
            '0' => [
                'info_name' => 'mme1',
                'info_value' =>'mme1',
                'info_set' => 'mme'
            ],
            '1' => [
                'info_name' => 'mme2',
                'info_value' =>'mme2',
                'info_set' => 'mme'
            ]
        ]);
    }

    /**
     * Test Conception Number: 14
     * Consult the mme_state information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_mme_state_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/mme_state');
        $response->assertJson([
            '0' => [
                'info_name' => 'mme_state1',
                'info_value' =>'mme_state1',
                'info_set' => 'mme_state'
            ],
            '1' => [
                'info_name' => 'mme_state2',
                'info_value' =>'mme_state2',
                'info_set' => 'mme_state'
            ]
        ]);
    }

    /**
     * Test Conception Number: 15
     * Consult the verif information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_verif_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/verif');
        $response->assertJson([
            '0' => [
                'info_name' => 'verif1',
                'info_value' =>'verif1',
                'info_set' => 'verif'
            ],
            '1' => [
                'info_name' => 'verif2',
                'info_value' =>'verif2',
                'info_set' => 'verif'
            ]
        ]);
    }

    /**
     * Test Conception Number: 16
     * Consult the verifRlz information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_verifRlz_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/verifRlz');
        $response->assertJson([
            '0' => [
                'info_name' => 'verifRlz1',
                'info_value' =>'verifRlz1',
                'info_set' => 'verifRlz'
            ],
            '1' => [
                'info_name' => 'verifRlz2',
                'info_value' =>'verifRlz2',
                'info_set' => 'verifRlz'
            ]
        ]);
    }

    /**
     * Test Conception Number: 17
     * Consult the mme_usage information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_mme_usage_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/mme_usage');
        $response->assertJson([
            '0' => [
                'info_name' => 'mme_usage1',
                'info_value' =>'mme_usage1',
                'info_set' => 'mme_usage'
            ],
            '1' => [
                'info_name' => 'mme_usage2',
                'info_value' =>'mme_usage2',
                'info_set' => 'mme_usage'
            ]
        ]);
    }

    /**
     * Test Conception Number: 18
     * Consult the mme_precaution information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_mme_precaution_information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/mme_precaution');
        $response->assertJson([
            '0' => [
                'info_name' => 'mme_precaution1',
                'info_value' =>'mme_precaution1',
                'info_set' => 'mme_precaution'
            ],
            '1' => [
                'info_name' => 'mme_precaution2',
                'info_value' =>'mme_precaution2',
                'info_set' => 'mme_precaution'
            ]
        ]);
    }

    /**
     * Test Conception Number: 19
     * Consult the enum_test_ information and checked if all the values are correct
     * Expected result: All the values are correctly received
     * @returns void
     */
    public function test_consult_enum_test__information()
    {
        if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        }
        $response = $this->get('/info/send/enum');
        $response->assertJson([
            '0' => [
                'info_name' => 'enum_test_1',
                'info_value' =>'enum_test_1'
            ],
            '1' => [
                'info_name' => 'enum_test_2',
                'info_value' =>'enum_test_2'
            ]
        ]);
    }

    /**
     * Test Conception Number: 20
     * Update an information
     * Expected result: The value is correctly update in the database
     * @returns void
     */
    public function test_update_information()
    {
        $this->assertEquals(Information::all()->count(), 0);
        //if (Information::all()->count() == 0) {
            $this->add_all_required_information();
        //}
        $response = $this->post('/info/update/'. Information::all()->first()->id, [
            'info_value' => 'edited'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('information', [
            'id' => Information::all()->first()->id,
            'info_value' => 'edited'
        ]);
    }

    
}
