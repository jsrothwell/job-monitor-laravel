<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'title', 'description', 'location', 'employment_type',
        'experience_level', 'job_url', 'external_id', 'salary_min', 'salary_max',
        'salary_currency', 'posted_date', 'expires_date', 'is_active',
        'first_seen_at', 'last_seen_at'
    ];

    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'posted_date' => 'date',
        'expires_date' => 'date',
        'is_active' => 'boolean',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function application()
    {
        return $this->hasOne(Application::class);
    }

    public function hasApplication()
    {
        return $this->application()->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTitle($query, $title)
    {
        return $query->where('title', 'LIKE', "%{$title}%");
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'LIKE', "%{$location}%");
    }
}
