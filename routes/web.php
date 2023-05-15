<?php

use App\Http\Controllers\SW03\AspectTestController;
use App\Http\Controllers\SW03\ComplementaryTestController;
use App\Http\Controllers\SW03\CriticalityController;
use App\Http\Controllers\SW03\DimensionnalTestController;
use App\Http\Controllers\SW03\DocControlController;
use App\Http\Controllers\SW03\FunctionnalTestController;
use App\Http\Controllers\SW03\IncmgInspController;
use App\Http\Controllers\SW03\SupplierAdrController;
use App\Http\Controllers\SW03\SupplierContactController;
use App\Http\Controllers\SW03\SupplierController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SW01\EquipmentController ;
use App\Http\Controllers\SW01\DimensionController ;
use App\Http\Controllers\SW01\PowerController ;
use App\Http\Controllers\SW01\SpecialProcessController ;
use App\Http\Controllers\SW01\UsageController ;
use App\Http\Controllers\FileController ;
use App\Http\Controllers\SW01\RiskController ;
use App\Http\Controllers\SW01\StateController ;
use App\Http\Controllers\SW01\PreventiveMaintenanceOperationController ;
use App\Http\Controllers\SW01\CurativeMaintenanceOperationController ;
use App\Http\Controllers\SW01\PreventiveMaintenanceOperationRealizedController ;
use App\Http\Controllers\SW01\EnumEquipmentTypeController ;
use App\Http\Controllers\SW01\EnumEquipmentMassUnitController ;
use App\Http\Controllers\SW01\EnumPowerTypeController ;
use App\Http\Controllers\SW01\EnumStateNameController ;
use App\Http\Controllers\SW01\EnumRiskForController ;
use App\Http\Controllers\SW01\EnumDimensionTypeController ;
use App\Http\Controllers\SW01\EnumDimensionNameController ;
use App\Http\Controllers\SW01\EnumDimensionUnitController ;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\SW01\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SW01\Auth\ConfirmablePasswordController;
use App\Http\Controllers\SW01\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\SW01\Auth\EmailVerificationPromptController;
use App\Http\Controllers\SW01\Auth\NewPasswordController;
use App\Http\Controllers\SW01\Auth\PasswordResetLinkController;
use App\Http\Controllers\SW01\Auth\RegisteredUserController;
use App\Http\Controllers\SW01\Auth\VerifyEmailController;
use App\Http\Controllers\UserController ;
use App\Http\Controllers\SW01\MmeController ;
use App\Http\Controllers\SW01\MmeTempController ;
use App\Http\Controllers\SW01\VerificationController ;
use App\Http\Controllers\SW01\VerificationRealizedController ;
use App\Http\Controllers\SW01\MmeUsageController ;
use App\Http\Controllers\SW01\MmeStateController ;
use App\Http\Controllers\SW01\PrecautionController ;
use App\Http\Controllers\SW01\EnumVerificationRequiredSkillController;
use App\Http\Controllers\SW01\EnumPrecautionTypeController;
use App\Http\Controllers\SW01\EnumUsageMetrologicalLevelController;
use App\Http\Controllers\SW01\EnumVerifAcceptanceAuthorityController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\SW03\CompFamilyController;
use App\Http\Controllers\SW03\EnumPurchasedByController;
use App\Http\Controllers\SW03\EnumStorageConditionController;
use App\Http\Controllers\SW03\RawFamilyController;
use App\Http\Controllers\SW03\ConsFamilyController;
use App\Http\Controllers\SW03\CompFamilyMemberController;
use App\Http\Controllers\SW03\ConsFamilyMemberController;
use App\Http\Controllers\SW03\RawFamilyMemberController;
use App\Http\Controllers\SW03\PurchaseSpecificationController;



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

Route::get('/SW01', function () {
    return view('welcomeSW01');
});

Route::get('/SW03', function () {
    return view('welcomeSW03');
});


Route::get('/dashboard', function () {
    return view('welcome');
})->middleware(['auth'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/equipment/add', function () {
        return view('welcomeSW01');
    });


    Route::get('/equipment/list', function () {
        return view('welcomeSW01');
    });
    Route::get('/equipment/list/PDF', function () {
        return view('welcomeSW01');
    });

    Route::get('/equipment/list/consult/{id}', function () {
        return view('welcomeSW01');
    });

    Route::get('equipment/history/{id}', function () {
        return view('welcomeSW01');
    });

    Route::get('mme/history/{id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/equipment/list/update/{id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/enum', function () {
        return view('welcome');
    });

    Route::get('/SW03/enum', function () {
        return view('welcomeSW03');
    });

    Route::get('/equipment/life_event', function () {
        return view('welcomeSW01');
    });

    Route::get('/equipment/life_event/state/{id}/{state_id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/equipment/life_event/state/{id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/equipment/life_event/all/{id}', function () {
        return view('welcomeSW01');
    });
    Route::get('/equipment/life_event/all/consult/{id}/{state_id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/equipment/life_event/reference/{id}/{state_id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/equipment/life_event/update/{id}/{state_id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/equipment/annual/planning', function () {
        return view('welcomeSW01');
    });
    Route::get('/equipment/monthly/planning', function () {
        return view('welcomeSW01');
    });

    Route::get('/equipment/lifesheet_pdf/{id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/equipment/reform/{id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/infos', function () {
        return view('welcome');
    });

    Route::get('/accounts', function () {
        return view('welcome');
    });

    Route::get('/my_account', function () {
        return view('welcome');
    });

    Route::get('/mme/add', function () {
        return view('welcomeSW01');
    });

    Route::get('/mme/list/consult/{id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/mme/list/update/{id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/mme/reform/{id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/mme/list/consult/{id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/mme/list/update/{id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/mme/reform/{id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/mme/list', function () {
        return view('welcomeSW01');
    });

    Route::get('/mme/life_event', function () {
        return view('welcomeSW01');
    });

    Route::get('/mme/life_event/reference/{id}/{state_id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/mme/life_event/state/{id}/{state_id}', function () {
        return view('welcomeSW01');
    });
    Route::get('/mme/life_event/state/{id}', function () {
        return view('welcomeSW01');
    });
    Route::get('/mme/life_event/all/{id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/mme/annual/planning', function () {
        return view('welcomeSW01');
    });
    Route::get('/mme/monthly/planning', function () {
        return view('welcomeSW01');
    });

    Route::get('/mme/life_event/update/{id}/{state_id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/mme/lifesheet_pdf/{id}', function () {
        return view('welcomeSW01');
    });

    Route::get('/supplier/add', function () {
        return view('welcomeSW03');
    });

    Route::get('/supplier/list', function () {
        return view('welcomeSW03');
    });

    Route::get('/supplier/list/consult/{id}', function () {
        return view('welcomeSW03');
    });

    Route::get('/supplier/list/update/{id}', function () {
        return view('welcomeSW03');
    });

    Route::get('/article/add', function () {
        return view('welcomeSW03');
    });

    Route::get('/article/list', function () {
        return view('welcomeSW03');
    });

    Route::get('/article/consult/{type}/{id}', function () {
        return view('welcomeSW03');
    });

    Route::get('/article/update/{type}/{id}', function () {
        return view('welcomeSW03');
    });
});


Route::get('/sign_up', function () {
    return view('welcome');
}) -> name('sign_up');

Route::get('/sign_in', function () {
    return view('welcome');
}) -> name('sign_in');







Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

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

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']) ->name('logout') ;
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

Route::get('/equipment/prvMtnOp/planning_monthly', [EquipmentController::class, 'send_eq_prvMtnOp_for_monthly_planning'] ) ;

Route::get('/equipment/prvMtnOp/revisionDatePassed', [EquipmentController::class, 'send_eq_prvMtnOp_revisionDatePassed'] ) ;

Route::get('/equipment/prvMtnOp/revisionLimitPassed ', [EquipmentController::class, 'send_eq_prvMtnOp_revisionLimitPassed'] ) ;

Route::post('/equipment/verifValidation/{id}', [EquipmentController::class, 'verif_validation'] ) ;

Route::post('/equipment/validation/{id}', [EquipmentController::class, 'validation'] ) ;

Route::post('/equipment/delete/', [EquipmentController::class, 'delete_equipment'] ) ;

Route::post('/state/equipment/{id} ', [EquipmentController::class, 'add_equipment_from_state'] ) ;

Route::get('/send/state/equipment/{state_id} ', [EquipmentController::class, 'send_equipment_from_state'] ) ;

Route::get('/send/equipment/planning/periode', [EquipmentController::class, 'send_periode_for_planning'] ) ;

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

Route::post('/equipment/add/file', [FileController::class, 'add_file_eq'])  ;

Route::post('/equipment/update/file/{id}', [FileController::class, 'update_file_eq'])  ;

Route::get('/file/send/{id}', [FileController::class, 'send_files_eq'])  ;

Route::post('/equipment/delete/file/{id}', [FileController::class, 'delete_file_eq'])  ;

Route::post('/mme/add/file', [FileController::class, 'add_file_mme'])  ;

Route::post('/mme/update/file/{id}', [FileController::class, 'update_file_mme'])  ;

Route::get('/file/send/mme/{id}', [FileController::class, 'send_files_mme'])  ;

Route::post('/mme/delete/file/{id}', [FileController::class, 'delete_file_mme'])  ;




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

Route::get('/prvMtnOp/send/revisionDatePassed/{id}', [PreventiveMaintenanceOperationController::class, 'send_prvMtnOp_from_eq_revisionDatePassed'])  ;

Route::get('/prvMtnOp/send/revisionTimeLimitPassed/{id}', [PreventiveMaintenanceOperationController::class, 'send_prvMtnOp_from_eq_revisionTimeLimitPassed'])  ;



/* Risk Form Routes */

Route::post('/risk/verif', [RiskController::class, 'verif_risk'])  ;

Route::post('/equipment/add/risk', [RiskController::class, 'add_risk_eqTemp'])  ;

Route::post('/equipment/update/risk/{id}', [RiskController::class, 'update_risk_eqTemp'])  ;

Route::get('/equipment/risk/send/{id}', [RiskController::class, 'send_risks_eqTemp'])  ;

Route::post('/equipment/delete/risk/{id}', [RiskController::class, 'delete_risk'])  ;

Route::post('/equipment/add/prvMtnOp/risk', [RiskController::class, 'add_risk_prvMtnOp'])  ;

Route::post('/equipment/update/prvMtnOp/risk/{id}', [RiskController::class, 'update_risk_prvMtnOp'])  ;

Route::get('/prvMtnOp/risk/send/{id}', [RiskController::class, 'send_risks_prvMtnOp'])  ;

Route::get('/prvMtnOp/risk/send/pdf/{id}', [RiskController::class, 'send_risks_pdf'])  ;


/* State Form Routes */

Route::post('/state/verif', [StateController::class, 'verif_state'])  ;

Route::post('/state/verif/beforeChangingState/{id}', [StateController::class, 'verif_before_changing_state'])  ;

Route::post('/state/verif/beforeReferencePrvOp/{id}', [StateController::class, 'verif_before_reference_prv_op'])  ;

Route::post('/state/verif/beforeReferenceCurOp/{id}', [StateController::class, 'verif_before_reference_cur_op'])  ;

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

Route::post('/prvMtnOpRlz/approve/{id}', [PreventiveMaintenanceOperationRealizedController::class, 'approve_prvMtnOpRlz'])  ;

Route::post('/prvMtnOpRlz/realize/{id}', [PreventiveMaintenanceOperationRealizedController::class, 'realize_prvMtnOpRlz'])  ;


/* Curative Maintenance Operation Form Routes */

Route::post('/curMtnOp/verif', [CurativeMaintenanceOperationController::class, 'verif_curMtnOp'])  ;

Route::post('/equipment/add/state/curMtnOp/', [CurativeMaintenanceOperationController::class, 'add_curMtnOp_eq'])  ;

Route::post('/equipment/update/state/curMtnOp/{id}', [CurativeMaintenanceOperationController::class,'update_curMtnOp_eq'])  ;

Route::get('/state/curMtnOp/send/{id}', [CurativeMaintenanceOperationController::class, 'send_curMtnOp_eq'])  ;

Route::post('/state/delete/curMtnOp/{id}', [CurativeMaintenanceOperationController::class, 'delete_curMtnOp'])  ;

Route::post('/curMtnOp/realize/{id}', [CurativeMaintenanceOperationController::class, 'realize_curMtnOp'])  ;

Route::post('curMtnOp/qualityVerifier/{id}', [CurativeMaintenanceOperationController::class, 'qualityVerification_curMtnOp'])  ;

Route::post('/curMtnOp/technicalVerifier/{id}', [CurativeMaintenanceOperationController::class, 'technicalVerification_curMtnOp'])  ;

Route::post('/mme/curMtnOp/verif', [CurativeMaintenanceOperationController::class, 'verif_curMtnOp_mme'])  ;

Route::post('/mme/add/state/curMtnOp/', [CurativeMaintenanceOperationController::class, 'add_curMtnOp_mme'])  ;

Route::post('/mme/update/state/curMtnOp/{id}', [CurativeMaintenanceOperationController::class,'update_curMtnOp_mme'])  ;

Route::get('/mme_state/curMtnOp/send/{id}', [CurativeMaintenanceOperationController::class, 'send_curMtnOp_mme'])  ;


/* History Form Routes */

Route::post('/history/add/equipment/{id}', [HistoryController::class, 'add_history_for_eq'])  ;

Route::get('/history/send/equipment/{id}', [HistoryController::class, 'send_history_for_eq'])  ;

Route::post('/history/add/mme/{id}', [HistoryController::class, 'add_history_for_mme'])  ;

Route::get('/history/send/mme/{id}', [HistoryController::class, 'send_history_for_mme'])  ;


/* Enum Form Routes */


Route::get('/equipment/enum/type', [EnumEquipmentTypeController::class, 'send_enum_type'] ) ;

Route::post('/equipment/enum/type/delete/{id}', [EnumEquipmentTypeController::class, 'delete_enum_type'] ) ;

Route::post('/equipment/enum/type/add', [EnumEquipmentTypeController::class, 'add_enum_type'] ) ;

Route::post('/equipment/enum/type/update/{id}', [EnumEquipmentTypeController::class, 'update_enum_type'] ) ;

Route::post('/equipment/enum/type/verif/{id}', [EnumEquipmentTypeController::class, 'verif_enum_type'] ) ;

Route::post('/equipment/enum/type/analyze/{id}', [EnumEquipmentTypeController::class, 'analyze_enum_type'] ) ;

Route::get('/equipment/enum/massUnit', [EnumEquipmentMassUnitController::class, 'send_enum_massUnit'] ) ;

Route::post('/equipment/enum/massUnit/delete/{id}', [EnumEquipmentMassUnitController::class, 'delete_enum_massUnit'] ) ;

Route::post('/equipment/enum/massUnit/add', [EnumEquipmentMassUnitController::class, 'add_enum_massUnit'] ) ;

Route::post('/equipment/enum/massUnit/update/{id}', [EnumEquipmentMassUnitController::class, 'update_enum_massUnit'] ) ;

Route::post('/equipment/enum/massUnit/verif/{id}', [EnumEquipmentMassUnitController::class, 'verif_enum_massUnit'] ) ;

Route::post('/equipment/enum/massUnit/analyze/{id}', [EnumEquipmentMassUnitController::class, 'analyze_enum_massUnit'] ) ;


Route::get('/dimension/enum/type', [EnumDimensionTypeController::class, 'send_enum_type'] ) ;

Route::post('/dimension/enum/type/delete/{id}', [EnumDimensionTypeController::class, 'delete_enum_type'] ) ;

Route::post('/dimension/enum/type/add', [EnumDimensionTypeController::class, 'add_enum_type'] ) ;

Route::post('/dimension/enum/type/update/{id}', [EnumDimensionTypeController::class, 'update_enum_type'] ) ;

Route::post('dimension/enum/type/verif/{id}', [EnumDimensionTypeController::class, 'verif_enum_type'] ) ;

Route::post('dimension/enum/type/analyze/{id}', [EnumDimensionTypeController::class, 'analyze_enum_type'] ) ;

Route::get('/dimension/enum/name', [EnumDimensionNameController::class, 'send_enum_name'] ) ;

Route::post('/dimension/enum/name/delete/{id}', [EnumDimensionNameController::class, 'delete_enum_name'] ) ;

Route::post('/dimension/enum/name/add', [EnumDimensionNameController::class, 'add_enum_name'] ) ;

Route::post('/dimension/enum/name/update/{id}', [EnumDimensionNameController::class, 'update_enum_name'] ) ;

Route::post('dimension/enum/name/verif/{id}', [EnumDimensionNameController::class, 'verif_enum_name'] ) ;

Route::post('dimension/enum/name/analyze/{id}', [EnumDimensionNameController::class, 'analyze_enum_name'] ) ;

Route::get('/dimension/enum/unit', [EnumDimensionUnitController::class, 'send_enum_unit'] ) ;

Route::post('/dimension/enum/unit/delete/{id}', [EnumDimensionUnitController::class, 'delete_enum_unit'] ) ;

Route::post('/dimension/enum/unit/add', [EnumDimensionUnitController::class, 'add_enum_unit'] ) ;

Route::post('/dimension/enum/unit/update/{id}', [EnumDimensionUnitController::class, 'update_enum_unit'] ) ;

Route::post('dimension/enum/unit/verif/{id}', [EnumDimensionUnitController::class, 'verif_enum_unit'] ) ;

Route::post('dimension/enum/unit/analyze/{id}', [EnumDimensionUnitController::class, 'analyze_enum_unit'] ) ;

Route::get('/power/enum/type', [EnumPowerTypeController::class, 'send_enum_type'])  ;

Route::post('/power/enum/type/delete/{id}', [EnumPowerTypeController::class, 'delete_enum_type'] ) ;

Route::post('/power/enum/type/add', [EnumPowerTypeController::class, 'add_enum_type'] ) ;

Route::post('power/enum/type/verif/{id}', [EnumPowerTypeController::class, 'verif_enum_type'] ) ;

Route::post('power/enum/type/analyze/{id}', [EnumPowerTypeController::class, 'analyze_enum_type'] ) ;

Route::post('/power/enum/type/update/{id}', [EnumPowerTypeController::class, 'update_enum_type'] ) ;

Route::get('/risk/enum/riskfor', [EnumRiskForController::class, 'send_enum_riskFor'] ) ;

Route::post('/risk/enum/riskfor/delete/{id}', [EnumRiskForController::class, 'delete_enum_riskFor'] ) ;

Route::post('/risk/enum/riskfor/add', [EnumRiskForController::class, 'add_enum_riskFor'] ) ;

Route::post('/risk/enum/riskfor/update/{id}', [EnumRiskForController::class, 'update_enum_riskFor'] ) ;

Route::post('risk/enum/riskfor/verif/{id}', [EnumRiskForController::class, 'verif_enum_riskFor'] ) ;

Route::post('risk/enum/riskfor/analyze/{id}', [EnumRiskForController::class, 'analyze_enum_riskFor'] ) ;

Route::get('/state/enum/name', [EnumStateNameController::class, 'send_enum_name'] ) ;

Route::post('/state/enum/name/delete/{id}', [EnumStateNameController::class, 'delete_enum_name'] ) ;

Route::post('/state/enum/name/add', [EnumStateNameController::class, 'add_enum_name'] ) ;

Route::post('/state/enum/name/update/{id}', [EnumStateNameController::class, 'update_enum_name'] ) ;

Route::get('/verification/enum/requiredSkill', [EnumVerificationRequiredSkillController::class, 'send_enum_verificationRequiredSkill'] ) ;

Route::post('/verification/enum/requiredSkill/delete/{id}', [EnumVerificationRequiredSkillController::class, 'delete_enum_requiredSkill'] ) ;

Route::post('/verification/enum/requiredSkill/add', [EnumVerificationRequiredSkillController::class, 'add_enum_requiredSkill'] ) ;

Route::post('/verification/enum/requiredSkill/update/{id}', [EnumVerificationRequiredSkillController::class, 'update_enum_requiredSkill'] ) ;

Route::post('verification/enum/requiredSkill/verif/{id}', [EnumVerificationRequiredSkillController::class, 'verif_enum_requiredSkill'] ) ;

Route::post('verification/enum/requiredSkill/analyze/{id}', [EnumVerificationRequiredSkillController::class, 'analyze_enum_requiredSkill'] ) ;

Route::get('/verification/enum/verifAcceptanceAuthority', [EnumVerifAcceptanceAuthorityController::class, 'send_enum_verifAcceptanceAuthority'] ) ;

Route::post('/verification/enum/verifAcceptanceAuthority/delete/{id}', [EnumVerifAcceptanceAuthorityController::class, 'delete_enum_verifAcceptanceAuthority'] ) ;

Route::post('/verification/enum/verifAcceptanceAuthority/add', [EnumVerifAcceptanceAuthorityController::class, 'add_enum_verifAcceptanceAuthority'] ) ;

Route::post('/verification/enum/verifAcceptanceAuthority/update/{id}', [EnumVerifAcceptanceAuthorityController::class, 'update_enum_verifAcceptanceAuthority'] ) ;

Route::post('verification/enum/verifAcceptanceAuthority/verif/{id}', [EnumVerifAcceptanceAuthorityController::class, 'verif_enum_verifAcceptanceAuthority'] ) ;

Route::post('verification/enum/verifAcceptanceAuthority/analyze/{id}', [EnumVerifAcceptanceAuthorityController::class, 'analyze_enum_verifAcceptanceAuthority'] ) ;

Route::get('/precaution/enum/type', [EnumPrecautionTypeController::class, 'send_enum_type'] ) ;

Route::post('/precaution/enum/type/delete/{id}', [EnumPrecautionTypeController::class, 'delete_enum_type'] ) ;

Route::post('/precaution/enum/type/add', [EnumPrecautionTypeController::class, 'add_enum_type'] ) ;

Route::post('precaution/enum/type/verif/{id}', [EnumPrecautionTypeController::class, 'verif_enum_type'] ) ;

Route::post('precaution/enum/type/analyze/{id}', [EnumPrecautionTypeController::class, 'analyze_enum_type'] ) ;

Route::post('/precaution/enum/type/update/{id}', [EnumPrecautionTypeController::class, 'update_enum_type'] ) ;

Route::get('/usage/enum/metrologicalLevel', [EnumUsageMetrologicalLevelController::class, 'send_enum_metrologicalLevel'] ) ;

Route::post('/usage/enum/metrologicalLevel/delete/{id}', [EnumUsageMetrologicalLevelController::class, 'delete_enum_metrologicalLevel'] ) ;

Route::post('/usage/enum/metrologicalLevel/add', [EnumUsageMetrologicalLevelController::class, 'add_enum_metrologicalLevel'] ) ;

Route::post('/usage/enum/metrologicalLevel/update/{id}', [EnumUsageMetrologicalLevelController::class, 'update_enum_metrologicalLevel'] ) ;

Route::post('usage/enum/metrologicalLevel/verif/{id}', [EnumUsageMetrologicalLevelController::class, 'verif_enum_metrologicalLevel'] ) ;

Route::post('usage/enum/metrologicalLevel/analyze/{id}', [EnumUsageMetrologicalLevelController::class, 'analyze_enum_metrologicalLevel'] ) ;




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

Route::get(' /info/send/mme_precaution', [InformationController::class, 'send_informations_mme_precaution']);


/* User Form Routes */

Route::get(' /users/send', [UserController::class, 'send_users']);

Route::post(' /user/update_right/user_menuUserAcessRight/{id}', [UserController::class, 'update_menuUserAcessRight']);

Route::post(' /user/update_right/user_resetUserPasswordRight/{id}', [UserController::class, 'update_resetUserPasswordRight']);

Route::post(' /user/update_right/user_updateDataInDraftRight/{id}', [UserController::class, 'update_updateDataInDraftRight']);

Route::post(' /user/update_right/user_validateDescriptiveLifeSheetDataRight/{id}', [UserController::class, 'update_validateDescriptiveLifeSheetDataRight']);

Route::post(' /user/update_right/user_validateOtherDataRight/{id}', [UserController::class, 'update_validateOtherDataRight']);

Route::post(' /user/update_right/user_updateDataValidatedButNotSignedRight/{id}', [UserController::class, 'update_updateDataValidatedButNotSignedRight']);

Route::post(' /user/update_right/user_updateDescriptiveLifeSheetDataSignedRight/{id}', [UserController::class, 'update_updateDescriptiveLifeSheetDataSignedRight']);

Route::post(' /user/update_right/user_makeQualityValidationRight/{id}', [UserController::class, 'update_makeQualityValidationRight']);

Route::post(' /user/update_right/user_makeTechnicalValidationRight/{id}', [UserController::class, 'update_makeTechnicalValidationRight']);

Route::post(' /user/update_right/user_makeEqOpValidationRight/{id}', [UserController::class, 'update_makeEqOpValidationRight']);

Route::post(' /user/update_right/user_makeMmeOpValidationRight/{id}', [UserController::class, 'update_makeMmeOpValidationRight']);

Route::post(' /user/update_right/user_updateEnumRight/{id}', [UserController::class, 'update_updateEnumRight']);

Route::post(' /user/update_right/user_deleteEnumRight/{id}', [UserController::class, 'update_deleteEnumRight']);

Route::post(' /user/update_right/user_addEnumRight/{id}', [UserController::class, 'update_addEnumRight']);

Route::post(' /user/update_right/user_deleteDataNotValidatedLinkedToEqOrMmeRight/{id}', [UserController::class, 'update_deleteDataNotValidatedLinkedToEqOrMmeRight']);

Route::post(' /user/update_right/user_deleteDataValidatedLinkedToEqOrMmeRight/{id}', [UserController::class, 'update_deleteDataValidatedLinkedToEqOrMmeRight']);

Route::post(' /user/update_right/user_deleteDataSignedLinkedToEqOrMmeRight/{id}', [UserController::class, 'update_deleteDataSignedLinkedToEqOrMmeRight']);

Route::post(' /user/update_right/user_deleteEqOrMmeRight/{id}', [UserController::class, 'update_deleteEqOrMmeRight']);

Route::post(' /user/update_right/user_updateInformationRight/{id}', [UserController::class, 'update_updateInformationRight']);

Route::post(' /user/update_right/user_personTrainedToGeneralPrinciplesOfEqManagementRight/{id}', [UserController::class, 'update_personTrainedToGeneralPrinciplesOfEqManagementRight']);

Route::post(' /user/update_right/user_personTrainedToGeneralPrinciplesOfMMEManagementRight/{id}', [UserController::class, 'update_personTrainedToGeneralPrinciplesOfMMEManagementRight']);

Route::post(' /user/update_right/user_makeEqRespValidationRight/{id}', [UserController::class, 'update_makeEqRespValidationRight']);

Route::post(' /user/update_right/user_makeMmeRespValidationRight/{id}', [UserController::class, 'update_makeMmeRespValidationRight']);

Route::post(' /user/update_right/user_makeReformRight/{id}', [UserController::class, 'update_makeReformRight']);

Route::post(' /user/update_right/user_declareNewStateRight/{id}', [UserController::class, 'update_declareNewStateRight']);

Route::post(' /user/update/infos/{id} ', [UserController::class, 'update_info']);

Route::post(' /user/update/myAccount/{id} ', [UserController::class, 'update_myAccount']);

Route::get(' /user/get/formationEqOk/{id} ', [UserController::class, 'formationEqOk']);

Route::get(' /user/get/formationMmeOk/{id} ', [UserController::class, 'formationMmeOk']);


/* Mme ID Form Routes */

Route::get('/mme/sets', [MmeController::class, 'send_sets'] ) ;

Route::post('/mme/add', [MmeController::class, 'add_mme']) ;

Route::post('/mme/verif', [MmeController::class, 'verif_mme'])  ;

Route::post('/mme/update/{id}', [MmeController::class, 'update_mme'])  ;

Route::get('/mme/mmes/', [MmeController::class, 'send_internalReferences_ids'] ) ;

Route::get('/mme/mmes_not_linked', [MmeController::class, 'send_mme_not_linked'] ) ;

Route::get('/mme/eq_linked/{id}', [MmeController::class, 'send_eq_linked_mme'] ) ;

Route::post('/mme/link_to_eq/{id}', [MmeController::class, 'link_mme_to_equipment'] ) ;

Route::post('/mme/delete/link_to_eq/{id}', [MmeController::class, 'delete_link_between_mme_to_equipment'] ) ;

Route::get('/mmes/same_set/{set}', [MmeController::class, 'send_mmes_same_set'])  ;

Route::get('/mme/{id}', [MmeController::class, 'send_mme'] )->whereNumber('id') ;

Route::get('/mme/verif/planning', [MmeController::class, 'send_mme_verif_for_planning'] ) ;

Route::get('/mme/verif/planning_monthly', [MmeController::class, 'send_mme_verif_for_monthly_planning'] ) ;

Route::post('/mme/verifValidation/{id}', [MmeController::class, 'verif_validation'] ) ;

Route::post('/mme/validation/{id}', [MmeController::class, 'validation'] ) ;

Route::post('/mme/delete/{id}', [MmeController::class, 'delete_mme'] ) ;

Route::post('/mme_state/mme/{id} ', [MmeController::class, 'add_mme_from_state'] ) ;

Route::get('/send/mme_state/mme/{state_id} ', [MmeController::class, 'send_mme_from_state'] ) ;

Route::get('/verif/send/revisionDatePassed', [MmeController::class, 'send_mme_verif_revisionDatePassed'])  ;

Route::get('/verif/send/revisionTimeLimitPassed', [MmeController::class, 'send_mme_verif_revisionLimitPassed'])  ;

Route::get('/mme/send/{id}', [MmeController::class, 'send_mmes']) ;

/* Mme State Form Routes */

Route::post('/mme_state/verif', [MmeStateController::class, 'verif_state'])  ;

Route::post('/mme_state/verif/beforeChangingState/{id}', [MmeStateController::class, 'verif_before_changing_state'])  ;

Route::post('/mme_state/verif/beforeReferenceVerif/{id}', [MmeStateController::class, 'verif_before_reference_verif'])  ;

Route::post('/mme_state/verif/beforeReferenceCurOp/{id}', [MmeStateController::class, 'verif_before_reference_cur_op'])  ;

Route::post('/mme/add/state', [MmeStateController::class, 'add_state'])  ;

Route::post('/mme/update/state/{id}', [MmeStateController::class, 'update_state'])  ;

Route::get('/mme_states/send/{id}', [MmeStateController::class, 'send_states'])  ;

Route::get('/mme_state/send/{id}', [MmeStateController::class, 'send_state'])  ;

Route::post('/mme/delete/state/{id}', [MmeStateController::class, 'delete_state'])  ;



/* Verification Form Routes */

Route::post('/verif/verif', [VerificationController::class, 'verif_verif'])  ;

Route::post('/mme/add/verif', [VerificationController::class, 'add_verif'])  ;

Route::post('/mme/update/verif/{id}', [VerificationController::class, 'update_verif'])  ;

Route::get('/verifs/send/{id}', [VerificationController::class, 'send_verifs'])  ;

Route::get('/verifs/send/lifesheet/{id}', [VerificationController::class, 'send_verifs_lifesheet'])  ;

Route::get('/verif/send/{id}', [VerificationController::class, 'send_verif'])  ;

Route::get('/verif/send/validated/{id}', [VerificationController::class, 'send_verif_from_mme_validated'])  ;

Route::get('/verif/send/validated/', [VerificationController::class, 'send_all_verif_validated'])  ;

Route::post('/mme/delete/verif/{id}', [VerificationController::class, 'delete_verif'])  ;

Route::post('/mme/reform/verif/{id}', [VerificationController::class, 'reform_verif'])  ;

/* Preventive Verification Realized Form Routes */

Route::post('/verifRlz/verif', [VerificationRealizedController::class, 'verif_verifRlz'])  ;

Route::post('/mme/add/mme_state/verifRlz', [VerificationRealizedController::class, 'add_verifRlz'])  ;

Route::post('/mme/update/mme_state/verifRlz/{id}', [VerificationRealizedController::class, 'update_verifRlz'])  ;

Route::get('/mme_state/verifRlz/send/{id}', [VerificationRealizedController::class, 'send_verifRlz'])  ;

Route::post('/mme_state/delete/verifRlz/{id}', [VerificationRealizedController::class, 'delete_verifRlz'])  ;

Route::post('/verifRlz/approve/{id}', [VerificationRealizedController::class, 'approve_verifRlz'])  ;

Route::post('/verifRlz/realize/{id}', [VerificationRealizedController::class, 'realize_verifRlz'])  ;

/* Mme Usage Form Routes */

Route::post('/mme_usage/verif', [MmeUsageController::class, 'verif_usage'])  ;

Route::post('/mme/add/usg', [MmeUsageController::class, 'add_usage'])  ;

Route::post('/mme/update/usg/{id}', [MmeUsageController::class, 'update_usage'])  ;

Route::get('/mme_usage/send/{id}', [MmeUsageController::class, 'send_usages'])  ;

Route::post('/mme/delete/usg/{id}', [MmeUsageController::class, 'delete_usage'])  ;

Route::post('/mme/reform/usg/{id}', [MmeUsageController::class, 'reform_usage'])  ;

/* Precaution Form Routes */

Route::post('/precaution/verif', [PrecautionController::class, 'verif_precaution'])  ;

Route::post('/mme/add/usage/prctn', [PrecautionController::class, 'add_precaution'])  ;

Route::post('/mme/update/prctn/{id}', [PrecautionController::class, 'update_precaution'])  ;

Route::post('/precaution/delete/{id}', [PrecautionController::class, 'delete_precaution'])  ;

Route::get('/precaution/send/{id}', [PrecautionController::class, 'send_precautions'])  ;

Route::get('/prctn/send/pdf/{id} ', [PrecautionController::class, 'send_precautions_pdf'])  ;

//SW03
//SupplierController
Route::post('/supplier/verif', [SupplierController::class, 'verif_supplier']);
Route::post('/supplier/add', [SupplierController::class, 'add_supplier']);
Route::get('/supplier/send', [SupplierController::class, 'send_suppliers']);
Route::get('/supplier/active/send', [SupplierController::class, 'send_active_suppliers']);
Route::get('/supplier/send/{id}', [SupplierController::class, 'send_supplier']);
Route::post('/supplier/update/{id}', [SupplierController::class, 'update_supplier']);
Route::post('/supplier/adr/verif', [SupplierAdrController::class, 'verif_adr']);
Route::post('/supplier/adr/add', [SupplierAdrController::class, 'add_adr']);
Route::get('/supplier/adr/send/{id}', [SupplierAdrController::class, 'send_adr']);
Route::post('/supplier/adr/update/{id}', [SupplierAdrController::class, 'update_adr']);
Route::post('/supplier/contact/verif', [SupplierContactController::class, 'verif_contact']);
Route::post('/supplier/contact/add', [SupplierContactController::class, 'add_contact']);
Route::get('/supplier/contact/send/{id}', [SupplierContactController::class, 'send_contact']);
Route::post('/supplier/contact/update/{id}', [SupplierContactController::class, 'update_contact']);

//ArticleController
Route::post('/comp/family/verif', [CompFamilyController::class, 'verif_compFamily']);
Route::post('/comp/family/add', [CompFamilyController::class, 'add_compFamily']);
Route::post('/comp/mb/verif', [CompFamilyMemberController::class, 'verif_compFamilyMember']);
Route::post('/comp/mb/add/{id}', [CompFamilyMemberController::class, 'add_compFamilyMember']);
Route::get('/comp/family/send', [CompFamilyController::class, 'send_compFamilies']);
Route::get('/comp/family/send/{id}', [CompFamilyController::class, 'send_compFamily']);
Route::get('/comp/mb/send/{id}', [CompFamilyMemberController::class, 'send_compFamilyMember']);
Route::post('/comp/family/update/{id}', [CompFamilyController::class, 'update_compFamily']);
Route::post('/comp/mb/update/{id}', [CompFamilyMemberController::class, 'update_compFamilyMember']);


Route::post('/cons/family/verif', [ConsFamilyController::class, 'verif_consFamily']);
Route::post('/cons/family/add', [ConsFamilyController::class, 'add_consFamily']);
Route::post('/cons/mb/verif', [ConsFamilyMemberController::class, 'verif_consFamilyMember']);
Route::post('/cons/mb/add/{id}', [ConsFamilyMemberController::class, 'add_consFamilyMember']);
Route::get('/cons/family/send', [ConsFamilyController::class, 'send_consFamilies']);
Route::get('/cons/family/send/{id}', [ConsFamilyController::class, 'send_consFamily']);
Route::get('/cons/mb/send/{id}', [ConsFamilyMemberController::class, 'send_consFamilyMember']);
Route::post('/cons/family/update/{id}', [ConsFamilyController::class, 'update_consFamily']);
Route::post('/cons/mb/update/{id}', [ConsFamilyMemberController::class, 'update_consFamilyMember']);



Route::post('/raw/family/verif', [RawFamilyController::class, 'verif_rawFamily']);
Route::post('/raw/family/add', [RawFamilyController::class, 'add_rawFamily']);
Route::post('/raw/mb/verif', [RawFamilyMemberController::class, 'verif_rawFamilyMember']);
Route::post('/raw/mb/add/{id}', [RawFamilyMemberController::class, 'add_rawFamilyMember']);
Route::get('/raw/family/send', [RawFamilyController::class, 'send_rawFamilies']);
Route::get('/raw/family/send/{id}', [RawFamilyController::class, 'send_rawFamily']);
Route::get('/raw/mb/send/{id}', [RawFamilyMemberController::class, 'send_rawFamilyMember']);
Route::post('/raw/family/update/{id}', [RawFamilyController::class, 'update_rawFamily']);
Route::post('/raw/mb/update/{id}', [RawFamilyMemberController::class, 'update_rawFamilyMember']);



// Incoming Inspection Controller
Route::post('/incmgInsp/verif', [IncmgInspController::class, 'verif_incmgInsp']);
Route::post('/incmgInsp/add', [IncmgInspController::class, 'add_incmgInsp']);
Route::post('/incmgInsp/update/{id}', [IncmgInspController::class, 'update_incmgInsp']);
Route::get('/incmgInsp/send', [IncmgInspController::class, 'send_incmgInsp']);
Route::get('/incmgInsp/send/raw/{id}', [IncmgInspController::class, 'send_incmgInspRaw']);
Route::get('/incmgInsp/send/comp/{id}', [IncmgInspController::class, 'send_incmgInspComp']);
Route::get('/incmgInsp/send/cons/{id}', [IncmgInspController::class, 'send_incmgInspCons']);

// Documentary Control Controller
Route::post('/incmgInsp/docControl/verif', [DocControlController::class, 'verif_docControl']);
Route::post('/incmgInsp/docControl/add', [DocControlController::class, 'add_docControl']);
Route::post('/incmgInsp/docControl/update/{id}', [DocControlController::class, 'update_docControl']);
Route::get('/incmgInsp/docControl/send/{id}', [DocControlController::class, 'send_docControl']);
Route::get('/incmgInsp/docControl/sendFromIncmgInsp/{id}', [DocControlController::class, 'send_docControlFromIncmgInsp']);

// Aspect Test Controller
Route::post('/incmgInsp/aspTest/verif', [AspectTestController::class, 'verif_aspectTest']);
Route::post('/incmgInsp/aspTest/add', [AspectTestController::class, 'add_aspectTest']);
Route::post('/incmgInsp/aspTest/update/{id}', [AspectTestController::class, 'update_aspectTest']);
Route::get('/incmgInsp/aspTest/send/{id}', [AspectTestController::class, 'send_aspectTest']);
Route::get('/incmgInsp/aspTest/sendFromIncmgInsp/{id}', [AspectTestController::class, 'send_aspectTestFromIncmgInsp']);

// Functionnal Test Controller
Route::post('/incmgInsp/funcTest/verif', [FunctionnalTestController::class, 'verif_funcTest']);
Route::post('/incmgInsp/funcTest/add', [FunctionnalTestController::class, 'add_funcTest']);
Route::post('/incmgInsp/funcTest/update/{id}', [FunctionnalTestController::class, 'update_funcTest']);
Route::get('/incmgInsp/funcTest/send/{id}', [FunctionnalTestController::class, 'send_funcTest']);
Route::get('/incmgInsp/funcTest/sendFromIncmgInsp/{id}', [FunctionnalTestController::class, 'send_funcTestFromIncmgInsp']);

// Dimensionnal Test Controller
Route::post('/incmgInsp/dimTest/verif', [DimensionnalTestController::class, 'verif_dimTest']);
Route::post('/incmgInsp/dimTest/add', [DimensionnalTestController::class, 'add_dimTest']);
Route::post('/incmgInsp/dimTest/update/{id}', [DimensionnalTestController::class, 'update_dimTest']);
Route::get('/incmgInsp/dimTest/send/{id}', [DimensionnalTestController::class, 'send_dimTest']);
Route::get('/incmgInsp/dimTest/sendFromIncmgInsp/{id}', [DimensionnalTestController::class, 'send_dimTestFromIncmgInsp']);

// Complementary Test Controller
Route::post('/incmgInsp/compTest/verif', [ComplementaryTestController::class, 'verif_compTest']);
Route::post('/incmgInsp/compTest/add', [ComplementaryTestController::class, 'add_compTest']);
Route::post('/incmgInsp/compTest/update/{id}', [ComplementaryTestController::class, 'update_compTest']);
Route::get('/incmgInsp/compTest/send/{id}', [ComplementaryTestController::class, 'send_compTest']);
Route::get('/incmgInsp/compTest/sendFromIncmgInsp/{id}', [ComplementaryTestController::class, 'send_compTestFromIncmgInsp']);

//Enum
Route::get('/artFam/enum/purchasedBy', [EnumPurchasedByController::class, 'send_enum_purchasedBy']);
Route::post('/artFam/enum/purchasedBy/add', [EnumPurchasedByController::class, 'add_enum_purchasedBy']);
Route::post('/artFam/enum/purchasedBy/verif/{id}', [EnumPurchasedByController::class, 'verif_enum_purchasedBy']);
Route::post('/artFam/enum/purchasedBy/update/{id}', [EnumPurchasedByController::class, 'update_enum_purchasedBy']);
Route::post('/artFam/enum/purchasedBy/disable/{id}', [EnumPurchasedByController::class, 'disable_enum_purchasedBy']);
Route::post('/artFam/enum/purchasedBy/enable/{id}', [EnumPurchasedByController::class, 'enable_enum_purchasedBy']);
Route::get('/artFam/enum/purchasedBy/sendUsage/{id}', [EnumPurchasedByController::class, 'send_enum_purchasedBy_usage']);
Route::get('/artFam/enum/storageCondition', [EnumStorageConditionController::class, 'send_enum_storageCondition']);
Route::post('/artFam/enum/storageCondition/add', [EnumStorageConditionController::class, 'add_enum_storageCondition']);
Route::post('/artFam/enum/storageCondition/verif/{id}', [EnumStorageConditionController::class, 'verif_enum_storageCondition']);
Route::post('/artFam/enum/storageCondition/link/{id}', [EnumStorageConditionController::class, 'link_enum_storageCondition']);
Route::get('/artFam/enum/storageCondition/send/{type}/{id}', [EnumStorageConditionController::class, 'send_enum_storageCondition_linked']);
Route::post('/artFam/enum/storageCondition/update/{type}/{id}', [EnumStorageConditionController::class, 'update_enum_storageCondition_linked']);
Route::post('/artFam/enum/storageCondition/unlink/{type}/{id}', [EnumStorageConditionController::class, 'unlink_enum_storageCondition']);
Route::post('/artFam/enum/storageCondition/delete/{id}', [EnumStorageConditionController::class, 'delete_enum_stoConds']);

// Criticality
Route::post('/artFam/criticality/verif', [CriticalityController::class, 'verif_criticality']);
Route::post('/artFam/criticality/add', [CriticalityController::class, 'add_criticality']);
Route::post('/artFam/criticality/update/{id}', [CriticalityController::class, 'update_criticality']);
Route::get('/artFam/criticality/send/{id}', [CriticalityController::class, 'send_criticality']);
Route::get('/artFam/criticality/send/{type}/{id}', [CriticalityController::class, 'send_criticalities']);


//purchase Specification
Route::post('/purSpe/verif', [PurchaseSpecificationController::class, 'verif_purSpe']);
Route::post('/purSpe/add/{id}', [PurchaseSpecificationController::class, 'add_purSpe']);
Route::post('/purSpe/update/{type}/{id}', [PurchaseSpecificationController::class, 'update_purSpe']);
Route::get('/purSpe/send/{id}', [PurchaseSpecificationController::class, 'send_purSpe']);
Route::get('/purSpe/send/{type}/{id}', [PurchaseSpecificationController::class, 'send_purSpes']);

//History for article
Route::post('/artFam/history/add/{type}/{id}', [HistoryController::class, 'add_history_for_article']);
Route::get('/artFam/history/send/{type}/{id}', [HistoryController::class, 'send_history_for_article']);


