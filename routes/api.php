<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarroController;
use Illuminate\Support\Facades\Storage;

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::group(['middleware' => 'auth.jwt'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'getAuthenticatedUser']);
        Route::get('users', [AuthController::class, 'listUsers']);
        Route::put('users/{user}', [AuthController::class, 'updateUser']);
        Route::delete('users/{user}', [AuthController::class, 'deleteUser']);
    });

    
});

Route::middleware(['auth.jwt'])->prefix('carros')->group(function () {
    Route::post('/', [CarroController::class, 'create']);
    Route::put('/{carro}', [CarroController::class, 'update']);
    Route::delete('/{id}', [CarroController::class, 'delete']);
    Route::get('/', [CarroController::class, 'list']);
    Route::get('/{id}', [CarroController::class, 'show']);
});

Route::get('/test-s3', function () {
    try {
        // Verifica se o bucket existe
        $exists = Storage::disk('s3')->exists('');
        if ($exists) {
            return response()->json(['message' => 'Conectado ao S3 e o bucket existe!'], 200);
        } else {
            return response()->json(['message' => 'Conectado ao S3, mas o bucket não foi encontrado.'], 404);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'Erro ao conectar com o S3: ' . $e->getMessage()], 500);
    }
});
