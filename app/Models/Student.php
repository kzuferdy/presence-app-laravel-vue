<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['name', 'nis', 'class', 'photo', 'address'];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
