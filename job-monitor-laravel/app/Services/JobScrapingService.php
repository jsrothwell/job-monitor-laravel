<?php
namespace App\Services;

use App\Models\Company;
use App\Models\Job;
use App\Models\ScrapingLog;
use App\Models\Application;
use Goutte\Client;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class JobScrapingService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function scrapeCompany(Company $company)
    {
        try {
            $crawler = $this->client->request('GET', $company->jobs_page_url);
            $config = $company->scraping_config;

            $jobsFound = 0;
            $newJobs = 0;
            $updatedJobs = 0;

            // Extract job listings based on configuration
            $jobElements = $crawler->filter($config['job_selector'] ?? '.job-listing');

            $jobElements->each(function ($node) use ($company, $config, &$jobsFound, &$newJobs, &$updatedJobs) {
                $jobsFound++;

                try {
                    $jobData = $this->extractJobData($node, $config, $company);

                    $existingJob = Job::where('company_id', $company->id)
                        ->where('external_id', $jobData['external_id'])
                        ->first();

                    if ($existingJob) {
                        // Check if job was reposted (disappeared and came back)
                        if (!$existingJob->is_active) {
                            $this->checkForRepost($existingJob);
                        }

                        $existingJob->update([
                            'last_seen_at' => now(),
                            'is_active' => true,
                        ]);
                        $updatedJobs++;
                    } else {
                        Job::create($jobData);
                        $newJobs++;
                    }
                } catch (\Exception $e) {
                    Log::error("Error processing job for {$company->name}: " . $e->getMessage());
                }
            });

            // Mark jobs not seen in this scrape as inactive
            Job::where('company_id', $company->id)
                ->where('last_seen_at', '<', now()->subMinutes(5))
                ->update(['is_active' => false]);

            $company->update(['last_scraped_at' => now()]);

            ScrapingLog::create([
                'company_id' => $company->id,
                'status' => 'success',
                'jobs_found' => $jobsFound,
                'new_jobs' => $newJobs,
                'updated_jobs' => $updatedJobs,
            ]);

            return ['success' => true, 'jobs_found' => $jobsFound, 'new_jobs' => $newJobs];

        } catch (\Exception $e) {
            Log::error("Scraping failed for {$company->name}: " . $e->getMessage());

            ScrapingLog::create([
                'company_id' => $company->id,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function extractJobData($node, $config, $company)
    {
        $title = $this->extractText($node, $config['title_selector'] ?? '.job-title');
        $location = $this->extractText($node, $config['location_selector'] ?? '.job-location');
        $jobUrl = $this->extractLink($node, $config['link_selector'] ?? 'a');

        // Make URL absolute if relative
        if (parse_url($jobUrl, PHP_URL_SCHEME) === null) {
            $jobUrl = rtrim($company->website_url, '/') . '/' . ltrim($jobUrl, '/');
        }

        return [
            'company_id' => $company->id,
            'title' => $title,
            'location' => $location,
            'job_url' => $jobUrl,
            'external_id' => $this->generateExternalId($jobUrl, $title),
            'first_seen_at' => now(),
            'last_seen_at' => now(),
            'is_active' => true,
        ];
    }

    protected function extractText($node, $selector)
    {
        try {
            return trim($node->filter($selector)->text());
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function extractLink($node, $selector)
    {
        try {
            return $node->filter($selector)->attr('href');
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function generateExternalId($url, $title)
    {
        // Try to extract ID from URL or create one from title
        if (preg_match('/\/job\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }

        return md5($url . $title);
    }

    protected function checkForRepost(Job $job)
    {
        $application = $job->application;
        if ($application && !$application->job_reposted) {
            $application->update([
                'job_reposted' => true,
                'job_reposted_at' => now(),
            ]);

            // Here you could send notification email/slack etc.
            Log::info("Job reposted: {$job->title} at {$job->company->name}");
        }
    }
}
