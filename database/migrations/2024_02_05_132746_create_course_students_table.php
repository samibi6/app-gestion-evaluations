<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->timestamp('date_eval')->nullable();
            $table->timestamps();

            $table->boolean('is_determining')->nullable();
            $table->boolean('is_other')->nullable();
            
            //adjournment form
            $table->timestamp('date_adjourned')->nullable();
            
            $table->date('adjournment_exam_date')->nullable();
            
            $table->boolean('adjournment_blunder_1')->nullable();
            $table->text('adjournment_blunder_1_justification')->nullable();
            
            $table->boolean('adjournment_blunder_2')->nullable();

            //denied form
            $table->timestamp('date_denied')->nullable();

            $table->date('denied_exam_date')->nullable();
            
            $table->boolean('denied_blunder_1')->nullable();
            $table->text('denied_blunder_1_justification')->nullable();
            
            $table->boolean('denied_blunder_2')->nullable();
            $table->text('denied_blunder_2_justification')->nullable();
           
            $table->boolean('denied_blunder_3')->nullable();
            
            $table->boolean('denied_blunder_4')->nullable();
            
            $table->boolean('denied_blunder_5')->nullable();
            
            $table->text('denied_justification_global')->nullable();
            
           
            //oui c'est une décharge publique mais plus le temps de faire une table supplémentaire, surtout que c'est du 1<->1
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_students');
    }
};
