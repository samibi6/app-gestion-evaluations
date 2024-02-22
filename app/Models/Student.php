<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['last_name', 'first_name'];

    public function courseSection()
    {
        return $this->hasMany(CriteriaStudent::class);
    }
    public function courseStudent()
    {
        return $this->hasMany(CourseStudent::class);
    }
    public function sectionStudent()
    {
        return $this->hasMany(SectionStudent::class);
    }
}
