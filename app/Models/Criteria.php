<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    use HasFactory;

    public function courseSections()
    {
        return $this->hasMany(CriteriaStudent::class);
    }
    public function aptitude()
    {
        return $this->belongsTo(Aptitude::class);
    }
     public function indicators()
    {
        return $this->hasMany(CriteriaStudent::class);
    }
}
