<?php
// routes/api.php
use App\Http\Controllers\Api\MidtransCallbackController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/midtrans/callback', [MidtransCallbackController::class, 'handle']);
