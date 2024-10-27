<?php

use App\Http\Controllers\ReviewPredictionController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('review');
});
Route::post('/predict-review', [ReviewPredictionController::class, 'trainAndPredict'])->name('predict-review');
Route::get('/predict-accuracy', [ReviewPredictionController::class, 'checkAccuracy'])->name('predict-accuracy');