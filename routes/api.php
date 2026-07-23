<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login', 'ApiController@login');

// hik vision fingerprint device api 
Route::post('/hikvision/callback', [ApiController::class, 'handleEvent'])
    ->middleware('verify.device.ip') // custom middleware
    ->name('hikvision.callback');


Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('logout', [ApiController::class, 'logout']);
    Route::get('get-projects', [ApiController::class, 'getProjects']);
    Route::post('add-tracker', [ApiController::class, 'addTracker']);
    Route::post('stop-tracker', [ApiController::class, 'stopTracker']);
    Route::post('upload-photos', [ApiController::class, 'uploadImage']);
});

// Recording API routes - protected by API key
Route::group(['middleware' => ['api.key']], function () {
    Route::get('recordings/test', [\App\Http\Controllers\RecordingApiController::class, 'test']);
    Route::get('recordings/to-download', [\App\Http\Controllers\RecordingApiController::class, 'getRecordingsToDownload']);
    Route::post('recordings/download', [\App\Http\Controllers\RecordingApiController::class, 'downloadRecordings']);
    Route::post('recordings/update-status', [\App\Http\Controllers\RecordingApiController::class, 'updateRecordingStatus']);
    Route::post('recordings/batch-update-status', [\App\Http\Controllers\RecordingApiController::class, 'batchUpdateRecordingStatus']);
    Route::post('avatar-leads/update-recording-link', [\App\Http\Controllers\RecordingApiController::class, 'updateAvatarLeadRecordingLink']);
    Route::get('avatar-leads/count', [\App\Http\Controllers\RecordingApiController::class, 'getAvatarLeadsCount']);
});
