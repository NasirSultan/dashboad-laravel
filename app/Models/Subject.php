<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Define the relationship between Subject and Classroom
    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }
}
