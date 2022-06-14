<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipmentController ; 
use App\Http\Controllers\DimensionController ; 
use App\Http\Controllers\PowerController ; 
use App\Http\Controllers\SpecialProcessController ; 
use App\Http\Controllers\UsageController ; 
use App\Http\Controllers\FileController ; 
use App\Http\Controllers\RiskController ; 
use App\Http\Controllers\StateController ; 
use App\Http\Controllers\PreventiveMaintenanceOperationController ;
use App\Http\Controllers\CurativeMaintenanceOperationController ;
use App\Http\Controllers\PreventiveMaintenanceOperationRealizedController ;
use App\Http\Controllers\EnumEquipmentTypeController ; 
use App\Http\Controllers\EnumEquipmentMassUnitController ; 
use App\Http\Controllers\EnumPowerTypeController ; 
use App\Http\Controllers\EnumStateNameController ; 
use App\Http\Controllers\EnumRiskForController ; 
use App\Http\Controllers\EnumDimensionTypeController ; 
use App\Http\Controllers\EnumDimensionNameController ; 
use App\Http\Controllers\EnumDimensionUnitController ; 
use App\Http\Controllers\InformationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('welcome');
})->middleware(['auth'])->name('dashboard');



Route::get('/equipment/add', function () {
    return view('welcome');
});
Route::get('/equipment/list', function () {
    return view('welcome');
});

Route::get('/equipment/list/consult/{id}', function () {
    return view('welcome');
});

Route::get('/equipment/list/update/{id}', function () {
    return view('welcome');
});

Route::get('/enum', function () {
    return view('welcome');
});

Route::get('/equipment/life_event', function () {
    return view('welcome');
});

Route::get('/equipment/life_event/state/{id}/{state_id}', function () {
    return view('welcome');
});

Route::get('/equipment/life_event/state/{id}', function () {
    return view('welcome');
});

Route::get('/equipment/life_event/all/{id}', function () {
    return view('welcome');
});
Route::get('/equipment/life_event/all/consult/{id}/{state_id}', function () {
    return view('welcome');
});

Route::get('/equipment/life_event/reference/{id}/{state_id}', function () {
    return view('welcome');
});

Route::get('/equipment/life_event/update/{id}/{state_id}', function () {
    return view('welcome');
});

Route::get('/equipment/maintenance/calendar', function () {
    return view('welcome');
});

Route::get('/equipment/lifesheet_pdf/{id}', function () {
    return view('welcome');
});

Route::get('/equipment/reform/{id}', function () {
    return view('welcome');
});

Route::get('/infos', function () {
    return view('welcome');
});

Route::get('/sign_up', function () {
    return view('welcome');
});

Route::get('/sign_in', function () {
    return view('welcome');
});


Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
                ->name('verification.notice');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});

/* Equipment ID Form Routes */ 



Route::get('/equipment/sets', [EquipmentController::class, 'send_sets'] ) ;

Route::post('/equipment/add', [EquipmentController::class, 'add_equipment'])  ->name('add_equipment');

Route::post('/equipment/verif', [EquipmentController::class, 'verif_equipment'])  ->name('verif_equipment');

Route::post('/equipment/update/{id}', [EquipmentController::class, 'update_equipment'])  ;

Route::get('/equipment/equipments', [EquipmentController::class, 'send_internalReferences_ids'] ) ;

Route::get('/equipments/same_set/{set}', [EquipmentController::class, 'send_equipments_same_set'])  ;

Route::get('/equipment/{id}', [EquipmentController::class, 'send_equipment'] )->whereNumber('id') ;

Route::get('/equipment/prvMtnOp/planning', [EquipmentController::class, 'send_eq_prvMtnOp_for_planning'] ) ;

Route::post('/equipment/verifValidation/{id}', [EquipmentController::class, 'verif_validation'] ) ;

Route::post('/equipment/validation/{id}', [EquipmentController::class, 'validation'] ) ;

Route::post('/equipment/delete/{id}', [EquipmentController::class, 'delete_equipment'] ) ;

Route::post('/state/equipment/{id} ', [EquipmentController::class, 'add_equipment_from_state'] ) ;

Route::get('/send/state/equipment/{state_id} ', [EquipmentController::class, 'send_equipment_from_state'] ) ;




/* Dimension Form Routes */ 

Route::post('/equipment/add/dim', [DimensionController::class, 'add_dimension'])  ;

Route::post('/equipment/update/dim/{id}', [DimensionController::class, 'update_dimension'])  ;

Route::get('/dimension/send/{id}', [DimensionController::class, 'send_dimensions'])  ;

Route::post('/dimension/verif', [DimensionController::class, 'verif_dimension'])  ;

Route::post('/equipment/delete/dim/{id}', [DimensionController::class, 'delete_dimension'])  ;

Route::get('/dimension/send/ByType/{id}', [DimensionController::class, 'send_dimensions_by_type'])  ;

/* Power Form Routes */ 

Route::get('/power/names', [PowerController::class, 'send_names'])  ;

Route::post('/power/verif', [PowerController::class, 'verif_power'])  ;

Route::post('/equipment/add/pow', [PowerController::class, 'add_power'])  ;

Route::post('/equipment/update/pow/{id}', [PowerController::class, 'update_power'])  ;

Route::get('/power/send/{id}', [PowerController::class, 'send_powers'])  ;

Route::post('/equipment/delete/pow/{id}', [PowerController::class, 'delete_power'])  ;

Route::get('/power/send/ByType/{id}', [PowerController::class, 'send_powers_by_type'])  ;

/* Special Process Form Routes */ 

Route::post('/spProc/verif', [SpecialProcessController::class, 'verif_specialProcess'])  ;

Route::post('/equipment/add/spProc', [SpecialProcessController::class, 'add_specialProcess'])  ;

Route::post('/equipment/update/spProc/{id}', [SpecialProcessController::class, 'update_specialProcess'])  ;

Route::get('/spProc/send/{id}', [SpecialProcessController::class, 'send_specialProcess'])  ;

Route::post('/equipment/delete/specialProcess{id}', [SpecialProcessController::class, 'delete_power'])  ;

/* Usage Form Routes */ 

Route::post('/usage/verif', [UsageController::class, 'verif_usage'])  ;

Route::post('/equipment/add/usg', [UsageController::class, 'add_usage'])  ;

Route::post('/equipment/update/usg/{id}', [UsageController::class, 'update_usage'])  ;

Route::get('/usage/send/{id}', [UsageController::class, 'send_usages'])  ;

Route::post('/equipment/delete/usg/{id}', [UsageController::class, 'delete_usage'])  ;

Route::post('/equipment/reform/usg/{id}', [UsageController::class, 'reform_usage'])  ;

/* File Form Routes */ 

Route::post('/file/verif', [FileController::class, 'verif_file'])  ;

Route::post('/equipment/add/file', [FileController::class, 'add_file'])  ;

Route::post('/equipment/update/file/{id}', [FileController::class, 'update_file'])  ;

Route::get('/file/send/{id}', [FileController::class, 'send_files'])  ;

Route::post('/equipment/delete/file/{id}', [FileController::class, 'delete_file'])  ;

/* Preventive Maintenance Operation Form Routes */ 

Route::post('/prvMtnOp/verif', [PreventiveMaintenanceOperationController::class, 'verif_prvMtnOp'])  ;

Route::post('/equipment/add/prvMtnOp', [PreventiveMaintenanceOperationController::class, 'add_prvMtnOp'])  ;

Route::post('/equipment/update/prvMtnOp/{id}', [PreventiveMaintenanceOperationController::class, 'update_prvMtnOp'])  ;

Route::get('/prvMtnOps/send/{id}', [PreventiveMaintenanceOperationController::class, 'send_prvMtnOps'])  ;

Route::get('/prvMtnOps/send/lifesheet/{id}', [PreventiveMaintenanceOperationController::class, 'send_prvMtnOps_lifesheet'])  ;

Route::get('/prvMtnOp/send/{id}', [PreventiveMaintenanceOperationController::class, 'send_prvMtnOp'])  ;

Route::get('/prvMtnOp/send/validated/{id}', [PreventiveMaintenanceOperationController::class, 'send_prvMtnOp_from_eq_validated'])  ;

Route::get('/prvMtnOp/send/validated/', [PreventiveMaintenanceOperationController::class, 'send_all_prvMtnOp_validated'])  ;

Route::post('/equipment/delete/prvMtnOp/{id}', [PreventiveMaintenanceOperationController::class, 'delete_prvMtnOp'])  ;

Route::post('/equipment/reform/prvMtnOp/{id}', [PreventiveMaintenanceOperationController::class, 'reform_prvMtnOp'])  ;


/* Risk Form Routes */ 

Route::post('/risk/verif', [RiskController::class, 'verif_risk'])  ;

Route::post('/equipment/add/risk', [RiskController::class, 'add_risk_eqTemp'])  ;

Route::post('/equipment/update/risk/{id}', [RiskController::class, 'update_risk_eqTemp'])  ;

Route::get('/equipment/risk/send/{id}', [RiskController::class, 'send_risks_eqTemp'])  ;

Route::post('/equipment/delete/risk/{id}', [RiskController::class, 'delete_risk'])  ;

Route::post('/equipment/add/prvMtnOp/risk', [RiskController::class, 'add_risk_prvMtnOp'])  ;

Route::post('/equipment/update/prvMtnOp/risk/{id}', [RiskController::class, 'update_risk_prvMtnOp'])  ;

Route::get('/prvMtnOp/risk/send/{id}', [RiskController::class, 'send_risks_prvMtnOp'])  ;


/* State Form Routes */ 

Route::post('/state/verif', [StateController::class, 'verif_state'])  ;

Route::post('/state/verif/beforeChangingState/{id}', [StateController::class, 'verif_before_changing_state'])  ;

Route::post('/state/verif/beforeReferenceOp/{id}', [StateController::class, 'verif_before_reference_op'])  ;

Route::post('/equipment/add/state', [StateController::class, 'add_state'])  ;

Route::post('/equipment/update/state/{id}', [StateController::class, 'update_state'])  ;

Route::get('/states/send/{id}', [StateController::class, 'send_states'])  ;

Route::get('/state/send/{id}', [StateController::class, 'send_state'])  ;

Route::post('/equipment/delete/state/{id}', [StateController::class, 'delete_state'])  ;


/* Preventive Maintenance Operation Realized Form Routes */ 

Route::post('/prvMtnOpRlz/verif', [PreventiveMaintenanceOperationRealizedController::class, 'verif_prvMtnOpRlz'])  ;

Route::post('/equipment/add/state/prvMtnOpRlz', [PreventiveMaintenanceOperationRealizedController::class, 'add_prvMtnOpRlz'])  ;

Route::post('/equipment/update/state/prvMtnOpRlz/{id}', [PreventiveMaintenanceOperationRealizedController::class, 'update_prvMtnOpRlz'])  ;

Route::get('/state/prvMtnOpRlz/send/{id}', [PreventiveMaintenanceOperationRealizedController::class, 'send_prvMtnOpRlz'])  ;

Route::post('/state/delete/prvMtnOpRlz/{id}', [PreventiveMaintenanceOperationRealizedController::class, 'delete_prvMtnOpRlz'])  ;

/* Curative Maintenance Operation Form Routes */ 

Route::post('/curMtnOp/verif', [CurativeMaintenanceOperationController::class, 'verif_curMtnOp'])  ;

Route::post('/equipment/add/state/curMtnOp/', [CurativeMaintenanceOperationController::class, 'add_curMtnOp'])  ;

Route::post('/equipment/update/state/curMtnOp/{id}', [CurativeMaintenanceOperationController::class,'update_curMtnOp'])  ;

Route::get('/state/curMtnOp/send/{id}', [CurativeMaintenanceOperationController::class, 'send_curMtnOp'])  ;

Route::post('/state/delete/curMtnOp/{id}', [CurativeMaintenanceOperationController::class, 'delete_curMtnOp'])  ;


/* Enum Form Routes */ 


Route::get('/equipment/enum/type', [EnumEquipmentTypeController::class, 'send_enum_type'] ) ;

Route::post('/equipment/enum/type/delete/{id}', [EnumEquipmentTypeController::class, 'delete_enum_type'] ) ;

Route::post('/equipment/enum/type/add', [EnumEquipmentTypeController::class, 'add_enum_type'] ) ;

Route::post('/equipment/enum/type/update/{id}', [EnumEquipmentTypeController::class, 'update_enum_type'] ) ;

Route::get('/equipment/enum/massUnit', [EnumEquipmentMassUnitController::class, 'send_enum_massUnit'] ) ;

Route::post('/equipment/enum/massUnit/delete/{id}', [EnumEquipmentMassUnitController::class, 'delete_enum_massUnit'] ) ;

Route::post('/equipment/enum/massUnit/add', [EnumEquipmentMassUnitController::class, 'add_enum_massUnit'] ) ;

Route::post('/equipment/enum/massUnit/update/{id}', [EnumEquipmentMassUnitController::class, 'update_enum_massUnit'] ) ;

Route::get('/dimension/enum/type', [EnumDimensionTypeController::class, 'send_enum_type'] ) ;

Route::post('/dimension/enum/type/delete/{id}', [EnumDimensionTypeController::class, 'delete_enum_type'] ) ;

Route::post('/dimension/enum/type/add', [EnumDimensionTypeController::class, 'add_enum_type'] ) ;

Route::post('/dimension/enum/type/update/{id}', [EnumDimensionTypeController::class, 'update_enum_type'] ) ;

Route::get('/dimension/enum/name', [EnumDimensionNameController::class, 'send_enum_name'] ) ;

Route::post('/dimension/enum/name/delete/{id}', [EnumDimensionNameController::class, 'delete_enum_name'] ) ;

Route::post('/dimension/enum/name/add', [EnumDimensionNameController::class, 'add_enum_name'] ) ;

Route::post('/dimension/enum/name/update/{id}', [EnumDimensionNameController::class, 'update_enum_name'] ) ;

Route::get('/dimension/enum/unit', [EnumDimensionUnitController::class, 'send_enum_unit'] ) ;

Route::post('/dimension/enum/unit/delete/{id}', [EnumDimensionUnitController::class, 'delete_enum_unit'] ) ;

Route::post('/dimension/enum/unit/add', [EnumDimensionUnitController::class, 'add_enum_unit'] ) ;

Route::post('/dimension/enum/unit/update/{id}', [EnumDimensionUnitController::class, 'update_enum_unit'] ) ;

Route::get('/power/enum/type', [EnumPowerTypeController::class, 'send_enum_type'])  ;

Route::post('/power/enum/type/delete/{id}', [EnumPowerTypeController::class, 'delete_enum_type'] ) ;

Route::post('/power/enum/type/add', [EnumPowerTypeController::class, 'add_enum_type'] ) ;

Route::post('/power/enum/type/update/{id}', [EnumPowerTypeController::class, 'update_enum_type'] ) ;

Route::get('/risk/enum/riskfor', [EnumRiskForController::class, 'send_enum_riskfor'] ) ;

Route::post('/risk/enum/riskfor/delete/{id}', [EnumRiskForController::class, 'delete_enum_riskfor'] ) ;

Route::post('/risk/enum/riskfor/add', [EnumRiskForController::class, 'add_enum_riskfor'] ) ;

Route::post('/risk/enum/riskfor/update/{id}', [EnumRiskForController::class, 'update_enum_riskfor'] ) ;

Route::get('/state/enum/name', [EnumStateNameController::class, 'send_enum_name'] ) ;

Route::post('/state/enum/name/delete/{id}', [EnumStateNameController::class, 'delete_enum_name'] ) ;

Route::post('/state/enum/name/add', [EnumStateNameController::class, 'add_enum_name'] ) ;

Route::post('/state/enum/name/update/{id}', [EnumStateNameController::class, 'update_enum_name'] ) ;


/* Information Form Routes */ 

Route::get('/info/send/all', [InformationController::class, 'send_informations_all'])  ;

Route::get('/info/send/eqIdCard', [InformationController::class, 'send_informations_eqIdCard'])  ;

Route::get('/info/send/dimension', [InformationController::class, 'send_informations_dimension'])  ;

Route::get('/info/send/power', [InformationController::class, 'send_informations_power'])  ;

Route::get('/info/send/specialProcess', [InformationController::class, 'send_informations_specialProcess'])  ;

Route::get('/info/send/usage', [InformationController::class, 'send_informations_usage'])  ;

Route::get('/info/send/file', [InformationController::class, 'send_informations_file']);

Route::get(' /info/send/preventiveMaintenanceOperation', [InformationController::class, 'send_informations_preventiveMaintenanceOperation']);

Route::get(' /info/send/risk', [InformationController::class, 'send_informations_risk']);

Route::get(' /info/send/state', [InformationController::class, 'send_informations_state']);

Route::get(' /info/send/state', [InformationController::class, 'send_informations_state']);

Route::get(' /info/send/preventiveMaintenanceOperationRealized', [InformationController::class, 'send_informations_preventiveMaintenanceOperationRealized']);

Route::get(' /info/send/curativeMaintenanceOperation', [InformationController::class, 'send_informations_curativeMaintenanceOperation']);

Route::get(' /info/send/enum', [InformationController::class, 'send_informations_enum']);

Route::post('/info/update/{id}', [InformationController::class, 'update_information'])  ;

Route::get(' /info/send/person', [InformationController::class, 'send_informations_person']);

Route::get(' /info/send/mme', [InformationController::class, 'send_informations_mme']);

Route::get(' /info/send/mme_state', [InformationController::class, 'send_informations_mme_state']);

Route::get(' /info/send/verif', [InformationController::class, 'send_informations_verif']);

Route::get(' /info/send/verifRlz', [InformationController::class, 'send_informations_verifRlz']);

Route::get(' /info/send/mme_usage', [InformationController::class, 'send_informations_mme_usage']);

Route::get(' /info/send/mme_precaution', [InformationController::class, 'send_informations_mme_usage']);


/* User Form Routes */ 

Route::get(' /users/send', [UserController::class, 'send_users']);

