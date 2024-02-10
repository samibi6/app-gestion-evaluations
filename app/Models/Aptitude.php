<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aptitude extends Model
{
    use HasFactory;
    public function criterias()
    {
        return $this->hasMany(Criteria::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}

