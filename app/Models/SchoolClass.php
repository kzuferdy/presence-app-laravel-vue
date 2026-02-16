<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $fillable = ['name', 'slug'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'school_class_user');
    }
}
