<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'status', 'jobs_found', 'new_jobs', 'updated_jobs', 'error_message'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
