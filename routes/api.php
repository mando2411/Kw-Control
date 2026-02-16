<?php

use App\Http\Controllers\Api\ContractorMobileController;
use Illuminate\Support\Facades\Route;

Route::prefix('contractor/{token}')->group(function () {
	Route::get('/bootstrap', [ContractorMobileController::class, 'bootstrap']);
	Route::get('/voters', [ContractorMobileController::class, 'voters']);
	Route::get('/voters/{voterId}', [ContractorMobileController::class, 'voterDetails']);
	Route::put('/voters/{voterId}/phone', [ContractorMobileController::class, 'updatePhone']);
	Route::put('/voters/{voterId}/percentage', [ContractorMobileController::class, 'updatePercentage']);
	Route::post('/voters/{voterId}/attachment', [ContractorMobileController::class, 'setVoterAttachment']);
});
