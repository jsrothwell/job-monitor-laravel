<?php
namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\JobScrapingService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount('activeJobs')->get();
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'website_url' => 'required|url',
            'jobs_page_url' => 'required|url',
            'job_selector' => 'required|string',
            'title_selector' => 'required|string',
            'location_selector' => 'string|nullable',
            'link_selector' => 'string|nullable',
        ]);

        $company = Company::create([
            'name' => $validated['name'],
            'website_url' => $validated['website_url'],
            'jobs_page_url' => $validated['jobs_page_url'],
            'scraping_config' => [
                'job_selector' => $validated['job_selector'],
                'title_selector' => $validated['title_selector'],
                'location_selector' => $validated['location_selector'] ?? '.location',
                'link_selector' => $validated['link_selector'] ?? 'a',
            ],
        ]);

        return redirect()->route('companies.index')->with('success', 'Company added successfully!');
    }

    public function scrape(Company $company, JobScrapingService $scrapingService)
    {
        $result = $scrapingService->scrapeCompany($company);

        if ($result['success']) {
            return back()->with('success', "Scraped {$result['jobs_found']} jobs, {$result['new_jobs']} new");
        } else {
            return back()->with('error', 'Scraping failed: ' . $result['error']);
        }
    }
}
