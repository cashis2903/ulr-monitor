<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MonitorController;

Route::get('monitors/{url}', [MonitorController::class, 'getSamplesByUrl'])->where('url', '.*');