<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('employment_type')->nullable(); // Full-time, Part-time, Contract
            $table->string('experience_level')->nullable();
            $table->string('job_url');
            $table->string('external_id')->nullable(); // Company's internal job ID
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('salary_currency', 3)->default('USD');
            $table->date('posted_date')->nullable();
            $table->date('expires_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('first_seen_at');
            $table->timestamp('last_seen_at');
            $table->timestamps();

            $table->index(['company_id', 'is_active']);
            $table->index(['title', 'location']);
            $table->unique(['company_id', 'external_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
