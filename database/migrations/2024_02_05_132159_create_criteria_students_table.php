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
        Schema::create('criteria_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criteria_id')->constrained()->onDelete('cascade');  
            $table->foreignId('student_id')->constrained()->onDelete('cascade');  
            $table->boolean('acquired');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria_students');
    }
};
