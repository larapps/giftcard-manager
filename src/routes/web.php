<?php

use Illuminate\Support\Facades\Route;
use Larapps\GiftCertificateManager\Controllers\IndexController;
use Larapps\GiftCertificateManager\Controllers\ImportController;
use Larapps\GiftCertificateManager\Controllers\ExportController;

Route::group(['prefix' => 'gift-certificates', 'middleware' => ['web']], function () {
    // Gift Certificates Listing Page.
    Route::get('/', [IndexController::class, 'index']);
    Route::get('/load', [IndexController::class, 'load']);

    // Gift Certificates Import Page
    Route::get('/import', [ImportController::class, 'index']);
    Route::get('/import/csv-template', [ImportController::class, 'formatDownload']);

    // To handle the import file.
    Route::post('/import', [ImportController::class, 'import']);
    Route::post('/import/{fileId}/acknowledge', [ImportController::class, 'acknowledge']);
    Route::get('/import/{fileId}/error-report', [ImportController::class, 'report']);

    // To Handle the export endpoints
    Route::get('/export', [ExportController::class, 'index']);
    Route::post('/export/start', [ExportController::class, 'start']);
    Route::post('/export/{fileId}/acknowledge', [ExportController::class, 'acknowledge']);

    Route::get('/export/file/exports/{exportFile}', [ExportController::class, 'download']);
});
