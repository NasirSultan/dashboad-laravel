<?php

// app/Models/Student.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['student_name', 'class_id'];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'student_subjects');
    }
}



