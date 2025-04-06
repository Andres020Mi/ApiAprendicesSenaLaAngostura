<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apprentice extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'full_name',
        'training_center',
        'photo_path',
        'start_date',
        'end_date',
        'program_name',
        'program_code',
        'blood_type', // Nuevo campo
    ];
}