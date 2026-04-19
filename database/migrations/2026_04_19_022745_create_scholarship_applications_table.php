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
        Schema::create('scholarship_applications', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('scholarship_id')->constrained('scholarships');
            $table->foreignUlid('grading_group_id')->constrained('grading_groups');

            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('city');
            $table->string('state');
            $table->string('gender');

            $table->boolean('ppot_member')->default(false);
            $table->string('ppot_mentor')->nullable();
            $table->boolean('prior_applicant')->default(false);
            $table->string('reference');

            $table->string('flight_school');
            $table->string('flight_training');
            $table->string('total_time');
            $table->string('flight_instruction');

            $table->string('education_level');
            $table->string('school');
            $table->string('graduation_month');
            $table->unsignedSmallInteger('graduation_year');
            $table->decimal('gpa', 3, 2);
            $table->text('academics');

            $table->text('short_term_goal');
            $table->text('long_term_goal');
            $table->text('career_aspirations');
            $table->boolean('has_received_awards')->default(false);
            $table->text('received_awards')->nullable();
            $table->text('other_organizations')->nullable();
            $table->text('volunteer_events')->nullable();
            $table->text('essay_one');
            $table->text('essay_two');

            $table->string('status')->default('pending');
            $table->string('flag_reason')->nullable();
            $table->unsignedTinyInteger('auto_score')->default(0);
            $table->unsignedTinyInteger('final_score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarship_applications');
    }
};
