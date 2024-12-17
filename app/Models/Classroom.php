<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    // Specify the table name if it's not the plural of the model name
    protected $table = 'classrooms';

    // Allow mass assignment for the following fields
    protected $fillable = ['name', 'student_id', 'subject_id'];

    // Disable timestamps if not required
    public $timestamps = false;
}
