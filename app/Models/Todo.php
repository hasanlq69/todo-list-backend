<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Todo extends Model
{
     use HasFactory;

    // Menentukan kolom yang bisa diisi secara massal
    protected $fillable = [
        'title',
        'assignee',
        'due_date',
        'time_tracked',
        'status',
        'priority',
    ];

    // Menentukan kolom yang akan di-cast ke tipe data tertentu
    protected $casts = [
        'due_date' => 'date',
    ];
}
