<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function courseSections()
    {
        return $this->hasMany(CourseSection::class);
    }
    public function sectionStudents()
    {
        return $this->hasMany(SectionStudent::class);
    }
}


