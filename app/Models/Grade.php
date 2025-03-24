<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Grade extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'student_id',
        'mark',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // A grade belongs to a teacher (User).
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // A grade belongs to a student (User).
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }


}
