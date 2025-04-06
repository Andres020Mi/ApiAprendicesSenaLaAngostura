<?php

use App\Http\Controllers\Api\ApprenticeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Ruta pública: Consultar un aprendiz por número de documento
Route::get('/apprentices/{documentNumber}', [ApprenticeController::class, 'show']);

// Ruta pública opcional: Subir foto (puedes protegerla si prefieres)
Route::post('/apprentices/{documentNumber}/photo', [ApprenticeController::class, 'uploadPhoto']);

// Ruta de login para administradores
Route::post('/admin/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    if (auth()->attempt($credentials)) {
        $user = auth()->user();
        $token = $user->createToken('admin-token')->plainTextToken;
        return response()->json(['message' => 'Login exitoso', 'token' => $token], 200);
    }
    return response()->json(['message' => 'Credenciales inválidas'], 401);
});

// Rutas protegidas para administradores
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/apprentices', [ApprenticeController::class, 'store']); // Crear aprendiz
    Route::post('/admin/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout exitoso'], 200);
    }); // Logout
});