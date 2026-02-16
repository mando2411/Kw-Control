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
	Route::post('/voters/attachment-bulk', [ContractorMobileController::class, 'setVotersAttachmentBulk']);

	Route::get('/groups', [ContractorMobileController::class, 'groups']);
	Route::post('/groups', [ContractorMobileController::class, 'createGroup']);
	Route::get('/groups/{groupId}', [ContractorMobileController::class, 'groupDetails']);
	Route::put('/groups/{groupId}', [ContractorMobileController::class, 'updateGroup']);
	Route::delete('/groups/{groupId}', [ContractorMobileController::class, 'deleteGroup']);
	Route::post('/groups/{groupId}/voters-action', [ContractorMobileController::class, 'groupVotersAction']);
});
