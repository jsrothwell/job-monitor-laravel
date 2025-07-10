<?php
namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::with('company')->active();

        if ($request->filled('title')) {
            $query->byTitle($request->title);
        }

        if ($request->filled('location')) {
            $query->byLocation($request->location);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $jobs = $query->orderBy('first_seen_at', 'desc')->paginate(20);

        return view('jobs.index', compact('jobs'));
    }

    public function show(Job $job)
    {
        $job->load('company', 'application');
        return view('jobs.show', compact('job'));
    }

    public function apply(Request $request, Job $job)
    {
        $validated = $request->validate([
            'notes' => 'string|nullable',
        ]);

        Application::create([
            'job_id' => $job->id,
            'applied_at' => now(),
            'notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Application tracked!');
    }

    public function applications()
    {
        $applications = Application::with('job.company')
            ->orderBy('applied_at', 'desc')
            ->paginate(20);

        return view('jobs.applications', compact('applications'));
    }
}
