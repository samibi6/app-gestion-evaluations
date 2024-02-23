<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['last_name', 'first_name'];

    public function courseSections()
    {
        return $this->hasMany(CriteriaStudent::class);
    }
    public function courseStudents()
    {
        return $this->hasMany(CourseStudent::class);
    }
    public function sectionStudents()
    {
        return $this->hasMany(SectionStudent::class);
    }
    public function proficiencyStudents()
    {
        return $this->hasMany(ProficiencyStudent::class);
    }
}
