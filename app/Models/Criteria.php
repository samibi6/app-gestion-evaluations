<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    use HasFactory;
    protected $fillable = ['description', 'aptitude_id'];

    public function courseSections()
    {
        return $this->hasMany(CriteriaStudent::class);
    }
    public function aptitude()
    {
        return $this->belongsTo(Aptitude::class);
    }
}
