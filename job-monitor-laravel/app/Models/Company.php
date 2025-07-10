<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'website_url', 'jobs_page_url', 'scraping_config', 'is_active', 'last_scraped_at'
    ];

    protected $casts = [
        'scraping_config' => 'array',
        'is_active' => 'boolean',
        'last_scraped_at' => 'datetime',
    ];

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function scrapingLogs()
    {
        return $this->hasMany(ScrapingLog::class);
    }

    public function activeJobs()
    {
        return $this->hasMany(Job::class)->where('is_active', true);
    }
}
