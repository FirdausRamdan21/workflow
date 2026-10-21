<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'nama',
        'deskripsi',
        'deadline',
        'status',
        'prioritas',
    ];

    protected $casts = [
        'deadline' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
