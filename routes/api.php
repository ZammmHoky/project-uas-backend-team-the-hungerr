<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Untuk HTTP client
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Models\Food;

// Route untuk register user
Route::post('register', [UserController::class, 'register']);

// Route untuk logout user
Route::post('logout', [UserController::class, 'logout']);

// Route untuk login user
Route::post('login', [UserController::class, 'login']);

// Route CRUD makanan
Route::apiResource('foods', FoodController::class);

// Route untuk mengirim pesan (hubungi kami)
Route::post('contacts', [ContactController::class, 'store']);
Route::get('contacts', [ContactController::class, 'index']);

// Route untuk predict makanan
Route::post('classify', function (Request $request) {
    if (!$request->hasFile('file')) {
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    $file = $request->file('file');

    // Simpan gambar yang di upload
    $uploadedImagePath = $file->store('public/classifications');
    $uploadedImageUrl = url('storage/classifications/' . basename($uploadedImagePath));

    $flaskUrl = 'http://127.0.0.1:5000/api/klasifikasi-makanan'; 
    $response = Http::attach(
        'file',
        file_get_contents($file->getRealPath()),
        $file->getClientOriginalName()
    )->post($flaskUrl);

    if ($response->successful()) {
        $result = $response->json();

        if (isset($result['predicted_class'])) {
            $foodName = $result['predicted_class'];

            $food = \App\Models\Food::where('name', $foodName)->first();

            if ($food) {
                return response()->json([
                    'message' => 'Klasifikasi berhasil!',
                    'data' => [
                        'name' => $food->name,
                        'description' => $food->description,
                        'image' => $uploadedImageUrl, 
                        'uploaded_image' => $uploadedImageUrl, 
                        'confidence' => $result['confidence'] ?? null,
                    ],
                ]);
            } else {
                return response()->json([
                    'message' => 'Data makanan tidak ditemukan dalam database.',
                    'classified_name' => $foodName,
                    'uploaded_image' => $uploadedImageUrl,
                    'confidence' => $result['confidence'] ?? null,
                ], 404);
            }
        } else {
            return response()->json([
                'error' => 'Invalid response from Flask API. Key "predicted_class" is missing.',
                'flask_response' => $result,
            ], 500);
        }
    } else {
        return response()->json(['error' => 'Failed to classify food'], 500);
    }
});



