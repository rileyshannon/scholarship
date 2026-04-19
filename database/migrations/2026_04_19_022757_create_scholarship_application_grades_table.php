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
        Schema::create('scholarship_application_grades', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('scholarship_application_id')->constrained('scholarship_applications');
            $table->foreignUlid('user_id')->constrained('users');
            $table->string('status')->default('active');
            $table->unsignedTinyInteger('short_term_goal_grade')->default(0);
            $table->text('short_term_goal_comments')->nullable();
            $table->unsignedTinyInteger('long_term_goal_grade')->default(0);
            $table->text('long_term_goal_comments')->nullable();
            $table->unsignedTinyInteger('received_awards_grade')->default(0);
            $table->text('received_awards_comments')->nullable();
            $table->unsignedTinyInteger('academics_grade')->default(0);
            $table->text('academics_comments')->nullable();
            $table->unsignedTinyInteger('other_organizations_grade')->default(0);
            $table->text('other_organizations_comments')->nullable();
            $table->unsignedTinyInteger('volunteer_events_grade')->default(0);
            $table->text('volunteer_events_comments')->nullable();
            $table->unsignedTinyInteger('career_progression_grade')->default(0);
            $table->text('career_progression_comments')->nullable();
            $table->unsignedTinyInteger('essay_one_grade')->default(0);
            $table->text('essay_one_comments')->nullable();
            $table->unsignedTinyInteger('essay_two_grade')->default(0);
            $table->text('essay_two_comments')->nullable();
            $table->unsignedSmallInteger('final_score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarship_application_grades');
    }
};
