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
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn('instructor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_user');

        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
        });
    }
};
