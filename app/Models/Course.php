<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public function courseSection()
    {
        return $this->hasMany(CourseSection::class);
    }
    public function courseStudent()
    {
        return $this->hasMany(CourseStudent::class);
    }
    public function courseUser()
    {
        return $this->hasMany(CourseUser::class);
    }
    public function aptitudes()
    {
        return $this->hasMany(Aptitude::class);
 }
    public function proficiencies()
    {
        return $this->hasMany(Proficiency::class);
 }
    }


