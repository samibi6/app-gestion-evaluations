<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseStudent extends Model
{
    use HasFactory;
    protected $fillable = ['course_id', 'student_id', 'date_eval' ,'is_determining', 'is_other', 'adjournment_exam_date' , 'adjournment_blunder_1', 'adjournment_blunder_1_justification', 
    'adjournment_blunder_2', 'date_adjourned', 'date_denied', 'denied_exam_date', 'denied_blunder_1', 'denied_blunder_1_justification', 'denied_blunder_2', 'denied_blunder_2_justification', 'denied_blunder_3', 'denied_blunder_4', 'denied_blunder_5', 'denied_justification_global' ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
