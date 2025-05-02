<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'frequency',
        'last_run_at',
        'next_run_at',
        'recipients',
        'filters',
        'user_id',
        'active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'recipients' => 'array',
        'filters' => 'array',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'active' => 'boolean',
    ];

    /**
     * Get the user who created the scheduled report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
