<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScrapingLogsTable extends Migration
{
    public function up()
    {
        Schema::create('scraping_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['success', 'failed', 'partial']);
            $table->integer('jobs_found')->default(0);
            $table->integer('new_jobs')->default(0);
            $table->integer('updated_jobs')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('scraping_logs');
    }
}
