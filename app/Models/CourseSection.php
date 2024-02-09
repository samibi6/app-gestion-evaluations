<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model
{
    use HasFactory;
    protected $fillable = ['year'];

    public function courses()
    {
        return $this->belongsTo(Course::class);
    }
    public function sections()
    {
        return $this->belongsTo(Section::class);
    }
}