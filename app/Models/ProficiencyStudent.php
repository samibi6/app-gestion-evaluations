<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProficiencyStudent extends Model
{
    use HasFactory;


    public function proficiency()
    {
        return $this->belongsTo(Proficiency::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
