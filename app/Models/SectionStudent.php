<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionStudent extends Model
{
    use HasFactory;
    
    protected $fillable = ['student_id', 'section_id'];
    
    public function sections()
    {
        return $this->belongsTo(Section::class);
    }
    public function students()
    {
        return $this->belongsTo(Student::class);
    }
}
