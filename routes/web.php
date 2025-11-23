<?php

use App\Http\Controllers\PaperController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PaperController::class, 'index'])->name('paper.form');
Route::post('/generate', [PaperController::class, 'generate'])->name('paper.generate');
Route::post('/preview', [PaperController::class, 'preview'])->name('paper.preview');
Route::post('/download', [PaperController::class, 'download'])->name('paper.download');
