<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CriteriaStudent extends Model
{
    use HasFactory;
    protected $fillable = ['criteria_id', 'student_id', 'acquired'];

    public function criteria()
    {
        return $this->belongsTo(Criteria::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
