<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user_Controller;
use App\Http\Controllers\user2_Controller;
use App\Http\Controllers\doctor_Controller;
use App\Http\Controllers\employe_auth_controller;
use App\Http\Controllers\start_app;
use App\Http\Controllers\doctor2_Controller;
use App\Http\Controllers\secrtary_Controller;
use App\Http\Controllers\labman_Controller;
use App\Http\Controllers\login_Controller;
use App\Http\Controllers\manager_controller;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group([
    // 'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [user_controller::class, 'user_login']);
    Route::post('/register', [user_controller::class, 'user_register']);
    Route::post('/logout', [user_controller::class, 'user_logout']);
    Route::get('/profile', [user_controller::class, 'user_Profile']);
    Route::post('/update', [user_controller::class, 'user_update']);
});


Route::group([
    'middleware' => 'api',
    'prefix' => 'user'
], function ($router) {
    Route::post('/create_group', [user2_controller::class, 'create_group']);
    Route::post('/mygroups', [user2_controller::class, 'mygroups']);
    Route::post('/doctors', [user2_controller::class, 'doctors']);
    Route::post('/create_interview', [user2_controller::class, 'create_interview']);
    Route::post('/myinterview', [user2_controller::class, 'myinterview']);
    Route::post('/all_doctors', [user2_controller::class, 'all_doctors']);
    Route::post('/doctor_files', [user2_controller::class, 'doctor_files']);
    Route::post('/download_file', [user2_controller::class, 'download_file']);
    Route::post('/create_request', [user2_controller::class, 'create_request']);
    Route::post('/update_request', [user2_controller::class, 'update_request']);
    Route::post('/my_request', [user2_controller::class, 'my_request']);


});



Route::group([
    // 'middleware' => 'doctor:doctor-api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/doctor_login', [doctor_controller::class, 'doctor_login']);
    Route::post('/doctor_register', [doctor_controller::class, 'doctor_register']);
    Route::post('/doctor_update', [doctor_controller::class, 'doctor_update']);
    Route::post('/doctor_logout', [doctor_controller::class, 'doctor_logout']);
    Route::get('/doctor_profile', [doctor_controller::class, 'doctor_Profile']);
});

Route::group([
    'middleware' => 'doctor:doctor-api',
    'prefix' => 'doctor'
], function ($router) {
    Route::post('/myinterview', [doctor2_controller::class, 'myinterview']);
    Route::post('/add_note', [doctor2_controller::class, 'add_note']);
    Route::post('/control_interview', [doctor2_controller::class, 'control_interview']);
    Route::post('/control_all_interview', [doctor2_controller::class, 'control_all_interviews']);
    Route::post('/change_interview', [doctor2_controller::class, 'change_interview']);
    Route::post('/notes', [doctor2_controller::class, 'notes']);
    Route::post('/my_program', [doctor2_controller::class, 'my_formal']);

    Route::post('/labs', [doctor2_controller::class, 'labs']);
    Route::post('/halls', [doctor2_controller::class, 'halls']);
    Route::post('/show_lab', [doctor2_controller::class, 'show_lab']);
    Route::post('/show_hall', [doctor2_controller::class, 'show_hall']);
    Route::post('/available_place', [doctor2_controller::class, 'available_place']);
    Route::post('/reserve_place', [doctor2_controller::class, 'reserve_place']);
    Route::post('/my_reserves', [doctor2_controller::class, 'my_reserves']);

    Route::post('/add_complaint', [doctor2_controller::class, 'add_complaint']);
    Route::post('/cancel_complaint', [doctor2_controller::class, 'cancel_complaint']);
    Route::post('/my_complaints', [doctor2_controller::class, 'my_complaints']);

    Route::post('/upload_file', [doctor2_controller::class, 'upload_file']);
    Route::post('/delete_file', [doctor2_controller::class, 'delete_file']);
    Route::post('/my_files', [doctor2_controller::class, 'my_files']);

});

Route::group([
    // 'middleware' => 'secrtary:secrtary-api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/employe_login', [employe_auth_controller::class, 'employe_login']);
    Route::post('/employe_logout', [employe_auth_controller::class, 'employe_logout']);
    Route::get('/employe_profile', [employe_auth_controller::class, 'employe_profile']);
});


Route::group([
    'middleware' => 'employe:employe-api',
    'prefix' => 'secrtary'
], function ($router) {
    Route::post('/codes', [secrtary_controller::class, 'codes']);
    Route::post('/myinterview', [secrtary_controller::class, 'myinterview']);
    Route::post('/doctor_interview', [secrtary_controller::class, 'doctor_interview']);
    Route::post('/complete_interview', [secrtary_controller::class, 'complete_interview']);
    Route::post('/getnotifications', [secrtary_controller::class, 'getnotifications']);
    Route::post('/doctors', [secrtary_controller::class, 'doctors']);
});


Route::group([
    'middleware' => 'employe:employe-api',
    'prefix' => 'labman'
], function ($router) {
    Route::post('/static_doctors', [labman_controller::class, 'static_doctors']);
    Route::post('/static_lectures', [labman_controller::class, 'static_lectures']);

    Route::post('/labs', [labman_controller::class, 'labs']);
    Route::post('/halls', [labman_controller::class, 'halls']);
    Route::post('/add_lab', [labman_controller::class, 'add_lab']);

    Route::post('/show_lab', [labman_controller::class, 'show_lab']);
    Route::post('/update_lab', [labman_controller::class, 'update_lab']);
    Route::post('/delete_lab', [labman_controller::class, 'delete_lab']);

    Route::post('/add_hall', [labman_controller::class, 'add_hall']);
    Route::post('/show_hall', [labman_controller::class, 'show_hall']);
    Route::post('/update_hall', [labman_controller::class, 'update_hall']);
    Route::post('/delete_hall', [labman_controller::class, 'delete_hall']);

    Route::post('/add_formal', [labman_controller::class, 'add_formal']);
    Route::post('/update_formal', [labman_controller::class, 'update_formal']);
    Route::post('/delete_formal', [labman_controller::class, 'delete_formal']);
    Route::post('/formal_day', [labman_controller::class, 'formal_day']);
    Route::post('/formal_place', [labman_controller::class, 'formal_place']);

    Route::post('/my_reserves', [labman_controller::class, 'my_reserves']);
    Route::post('/control_reserve', [labman_controller::class, 'control_reserve']);

    Route::post('/my_complaints', [labman_controller::class, 'my_complaints']);
    Route::post('/control_complaints', [labman_controller::class, 'control_complaints']);

});

Route::group([
    'middleware' => 'employe:employe-api',
    'prefix' => 'manager'
], function ($router) {

    Route::post('/get_requests', [manager_controller::class, 'get_requests']);
    Route::post('/get_req_state', [manager_controller::class, 'get_req_state']);
    Route::post('/get_req_type', [manager_controller::class, 'get_req_type']);
    Route::post('/search', [manager_controller::class, 'search']);
    Route::post('/update_state', [manager_controller::class, 'update_state']);

});

Route::group([
    'prefix' => 'start'
], function ($router) {
    Route::post('/add_employe', [start_app::class, 'add_employe']);

});


Route::group([
    'prefix' => 'all'
], function ($router) {
    Route::post('/login', [login_controller::class, 'login']);

});
