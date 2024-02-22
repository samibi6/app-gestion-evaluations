<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proficiency extends Model
{
    use HasFactory;

    protected $fillable = ['criteria', 'indicator', 'score'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
