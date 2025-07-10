<?php
namespace App\Console\Commands;

use App\Models\Company;
use App\Services\JobScrapingService;
use Illuminate\Console\Command;

class ScrapeJobs extends Command
{
    protected $signature = 'jobs:scrape {company_id?}';
    protected $description = 'Scrape job postings from companies';

    public function handle(JobScrapingService $scrapingService)
    {
        $companyId = $this->argument('company_id');

        if ($companyId) {
            $companies = Company::where('id', $companyId)->where('is_active', true)->get();
        } else {
            $companies = Company::where('is_active', true)->get();
        }

        $this->info("Starting to scrape " . $companies->count() . " companies...");

        foreach ($companies as $company) {
            $this->info("Scraping {$company->name}...");
            $result = $scrapingService->scrapeCompany($company);

            if ($result['success']) {
                $this->info("✓ Found {$result['jobs_found']} jobs, {$result['new_jobs']} new");
            } else {
                $this->error("✗ Failed: {$result['error']}");
            }
        }

        $this->info("Scraping completed!");
    }
}
