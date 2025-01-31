<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    // Field yang dapat diisi (mass assignable)
    protected $fillable = [
        'title', 
        'description', 
        'status', 
        'due_date', 
        'user_id', 
        'assignee_id'
    ];

    /**
     * Relasi ke model User sebagai pemilik tugas (user_id).
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke model User sebagai penerima tugas (assignee_id).
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * Relasi ke tabel logs untuk mencatat riwayat aktivitas.
     */
    public function logs()
    {
        return $this->hasMany(Log::class);
    }

}
