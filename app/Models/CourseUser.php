<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseUser extends Model
{
    use HasFactory;

    public function courses()
    {
        return $this->belongsTo(Course::class);
    }
    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
