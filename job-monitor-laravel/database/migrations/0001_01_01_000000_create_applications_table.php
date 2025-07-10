<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->timestamp('applied_at');
            $table->enum('status', ['applied', 'interview', 'rejected', 'offer', 'withdrawn'])->default('applied');
            $table->text('notes')->nullable();
            $table->boolean('job_reposted')->default(false);
            $table->timestamp('job_reposted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('applications');
    }
}
