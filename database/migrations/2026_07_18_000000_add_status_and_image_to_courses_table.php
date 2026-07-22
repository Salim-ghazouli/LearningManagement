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
            if (!Schema::hasColumn('courses', 'status')) {
                $table->enum('status', ['draft', 'published'])->default('draft')->after('is_free');
            }
            if (!Schema::hasColumn('courses', 'course_image')) {
                $table->string('course_image')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('courses', 'course_image')) {
                $table->dropColumn('course_image');
            }
        });
    }
};
