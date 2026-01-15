<?php

// app/Modules/Attendance/Models/Student.php
namespace App\Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['name', 'nis', 'class'];
}
