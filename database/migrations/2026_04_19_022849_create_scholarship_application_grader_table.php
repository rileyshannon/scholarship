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
        Schema::create('scholarship_application_grader', function (Blueprint $table) {
            $table->foreignUlid('scholarship_application_id')->constrained('scholarship_applications');
            $table->foreignUlid('user_id')->constrained('users');
            $table->timestamp('assigned_at')->useCurrent();
            $table->primary(['scholarship_application_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarship_application_grader');
    }
};
