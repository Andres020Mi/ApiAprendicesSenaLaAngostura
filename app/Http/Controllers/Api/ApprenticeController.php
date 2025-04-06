<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apprentice;
use Illuminate\Http\Request;

class ApprenticeController extends Controller
{
    /**
     * Mostrar un aprendiz por número de documento
     */
    public function show($documentNumber)
    {
        $apprentice = Apprentice::where('document_number', $documentNumber)->first();

        if (!$apprentice) {
            return response()->json([
                'message' => 'Aprendiz no encontrado',
                'status' => 404,
            ], 404);
        }

        // URL base del dominio sin /public para imágenes
        $baseUrl = 'https://apiaprendizes.tiny-purchases.com';
        $senaLogoUrl = $baseUrl . '/storage/app/public/sena-logo.png'; // Ruta directa al logo
        $photoUrl = $baseUrl . '/storage/app/public/' . $apprentice->photo_path; // Ruta directa a la foto

        $data = [
            'message' => 'Información del aprendiz encontrada',
            'status' => 200,
            'data' => [
                'document_number' => $apprentice->document_number,
                'full_name' => $apprentice->full_name,
                'training_center' => $apprentice->training_center,
                'photo_url' => $photoUrl,
                'sena_logo_url' => $senaLogoUrl,
                'start_date' => $apprentice->start_date,
                'end_date' => $apprentice->end_date,
                'program_name' => $apprentice->program_name,
                'program_code' => $apprentice->program_code,
                'blood_type' => $apprentice->blood_type,
            ]
        ];

        return response()->json($data, 200);
    }

    /**
     * Crear un nuevo aprendiz (protegido para administradores)
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'document_number' => 'required|string|unique:apprentices,document_number',
            'full_name' => 'required|string|max:255',
            'training_center' => 'required|string|max:255',
            'photo' => 'required|image|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'program_name' => 'required|string|max:255',
            'program_code' => 'required|string|max:50',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'status' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        // Guardar la foto en storage/app/public/apprentices
        $photoPath = $request->file('photo')->store('apprentices', 'public');

        // Crear el aprendiz
        $apprentice = Apprentice::create([
            'document_number' => $request->document_number,
            'full_name' => $request->full_name,
            'training_center' => $request->training_center,
            'photo_path' => $photoPath,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'program_name' => $request->program_name,
            'program_code' => $request->program_code,
            'blood_type' => $request->blood_type,
        ]);

        // Generar la URL de la foto para la respuesta
        $baseUrl = 'https://apiaprendizes.tiny-purchases.com';
        $photoUrl = $baseUrl . '/storage/app/public/' . $photoPath;

        return response()->json([
            'message' => 'Aprendiz creado con éxito',
            'status' => 201,
            'data' => array_merge($apprentice->toArray(), ['photo_url' => $photoUrl]),
        ], 201);
    }

    /**
     * Subir foto
     */
    public function uploadPhoto(Request $request, $documentNumber)
    {
        $request->validate([
            'photo' => 'required|image|max:2048',
        ]);

        $apprentice = Apprentice::where('document_number', $documentNumber)->first();

        if (!$apprentice) {
            return response()->json(['message' => 'Aprendiz no encontrado'], 404);
        }

        $path = $request->file('photo')->store('apprentices', 'public');
        $apprentice->photo_path = $path;
        $apprentice->save();

        // Generar la URL de la foto para la respuesta
        $baseUrl = 'https://apiaprendizes.tiny-purchases.com';
        $photoUrl = $baseUrl . '/storage/app/public/' . $path;

        return response()->json([
            'message' => 'Foto subida con éxito',
            'photo_url' => $photoUrl,
        ], 200);
    }
}