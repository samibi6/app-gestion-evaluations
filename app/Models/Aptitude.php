<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aptitude extends Model
{
    use HasFactory;
    protected $fillable = ['description', 'course_id'];
    public function criterias()
    {
        return $this->hasMany(Criteria::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}

