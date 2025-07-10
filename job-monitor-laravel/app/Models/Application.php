<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id', 'applied_at', 'status', 'notes', 'job_reposted', 'job_reposted_at'
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'job_reposted' => 'boolean',
        'job_reposted_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
