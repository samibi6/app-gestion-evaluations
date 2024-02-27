<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proficiency extends Model
{
    use HasFactory;

    protected $fillable = ['criteria_skill', 'indicator', 'course_id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function proficiencyStudents()
    {
        return $this->hasMany(ProficiencyStudent::class);
    }
}
