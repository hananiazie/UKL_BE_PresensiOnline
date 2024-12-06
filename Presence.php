<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $table = 'attendances'; // Nama tabel

    protected $fillable = [
        'user_id',
        'date',
        'time',
        'status',
    ];

    // Relasi dengan model User (jika ada)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
