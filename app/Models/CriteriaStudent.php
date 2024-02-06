<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CriteriaStudent extends Model
{
    use HasFactory;

    public function criterias()
    {
        return $this->belongsTo(Criteria::class);
    }
    public function students()
    {
        return $this->belongsTo(Student::class);
    }
}
