<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CompanyController;

Route::get('/', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
Route::post('/jobs/{job}/apply', [JobController::class, 'apply'])->name('jobs.apply');
Route::get('/applications', [JobController::class, 'applications'])->name('jobs.applications');

Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
Route::post('/companies/{company}/scrape', [CompanyController::class, 'scrape'])->name('companies.scrape');
