<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionRuleConflict extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rule_a_id',
        'rule_b_id',
        'conflict_type',
        'details',
        'resolved',
        'resolved_at',
        'resolved_by',
        'resolution_type',
        'resolution_data',
        'resolution_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'details' => 'array',
        'resolution_data' => 'array',
        'resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the first rule involved in the conflict.
     */
    public function ruleA(): BelongsTo
    {
        return $this->belongsTo(CommissionRule::class, 'rule_a_id');
    }

    /**
     * Get the second rule involved in the conflict.
     */
    public function ruleB(): BelongsTo
    {
        return $this->belongsTo(CommissionRule::class, 'rule_b_id');
    }

    /**
     * Get the user who resolved the conflict.
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Get a human-readable description of the conflict.
     */
    public function getDescriptionAttribute(): string
    {
        switch ($this->conflict_type) {
            case 'type_mismatch':
                return sprintf(
                    'Rules have different commission types (%s vs %s)',
                    $this->details['type_mismatch']['rule_a_type'],
                    $this->details['type_mismatch']['rule_b_type']
                );

            case 'value_conflict':
                return sprintf(
                    'Rules have different commission values (difference: %s)',
                    $this->details['value_conflict']['value_difference']
                );

            case 'date_overlap':
                return sprintf(
                    'Rules have overlapping effective dates (%s to %s)',
                    $this->details['date_overlap']['start'],
                    $this->details['date_overlap']['end']
                );

            case 'condition_overlap':
                $conditions = array_keys($this->details['condition_overlap']);
                return sprintf(
                    'Rules have overlapping conditions (%s)',
                    implode(', ', $conditions)
                );

            default:
                return 'Unknown conflict type';
        }
    }

    /**
     * Get a human-readable resolution description.
     */
    public function getResolutionDescriptionAttribute(): ?string
    {
        if (!$this->resolved) {
            return null;
        }

        switch ($this->resolution_type) {
            case 'adjust_conditions':
                return 'Resolved by adjusting rule conditions';

            case 'adjust_values':
                return 'Resolved by adjusting commission values';

            case 'adjust_dates':
                return 'Resolved by adjusting effective dates';

            default:
                return 'Unknown resolution type';
        }
    }

    /**
     * Get the severity level of the conflict.
     */
    public function getSeverityAttribute(): string
    {
        switch ($this->conflict_type) {
            case 'type_mismatch':
                return 'high';

            case 'value_conflict':
                $difference = $this->details['value_conflict']['value_difference'];
                return $difference > 5 ? 'high' : 'medium';

            case 'date_overlap':
                return 'medium';

            case 'condition_overlap':
                return count($this->details['condition_overlap']) > 1 ? 'high' : 'medium';

            default:
                return 'low';
        }
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->resolved) {
            return 'green';
        }

        return match ($this->severity) {
            'high' => 'red',
            'medium' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Scope a query to only include unresolved conflicts.
     */
    public function scopeUnresolved($query)
    {
        return $query->where('resolved', false);
    }

    /**
     * Scope a query to only include resolved conflicts.
     */
    public function scopeResolved($query)
    {
        return $query->where('resolved', true);
    }

    /**
     * Scope a query to only include conflicts involving a specific rule.
     */
    public function scopeInvolvingRule($query, CommissionRule $rule)
    {
        return $query->where(function ($q) use ($rule) {
            $q->where('rule_a_id', $rule->getKey())
                ->orWhere('rule_b_id', $rule->getKey());
        });
    }

    /**
     * Scope a query to only include conflicts of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('conflict_type', $type);
    }

    /**
     * Scope a query to only include conflicts with specific severity.
     */
    public function scopeWithSeverity($query, string $severity)
    {
        return $query->where(function ($q) use ($severity) {
            switch ($severity) {
                case 'high':
                    $q->where('conflict_type', 'type_mismatch')
                        ->orWhere(function ($q) {
                            $q->where('conflict_type', 'value_conflict')
                                ->whereRaw("JSON_EXTRACT(details, '$.value_conflict.value_difference') > ?", [5]);
                        })
                        ->orWhere(function ($q) {
                            $q->where('conflict_type', 'condition_overlap')
                                ->whereRaw("JSON_LENGTH(JSON_EXTRACT(details, '$.condition_overlap')) > ?", [1]);
                        });
                    break;

                case 'medium':
                    $q->where(function ($q) {
                        $q->where('conflict_type', 'value_conflict')
                            ->whereRaw("JSON_EXTRACT(details, '$.value_conflict.value_difference') <= ?", [5]);
                    })
                        ->orWhere('conflict_type', 'date_overlap')
                        ->orWhere(function ($q) {
                            $q->where('conflict_type', 'condition_overlap')
                                ->whereRaw("JSON_LENGTH(JSON_EXTRACT(details, '$.condition_overlap')) = ?", [1]);
                        });
                    break;

                default:
                    $q->whereNotIn('conflict_type', ['type_mismatch', 'value_conflict', 'date_overlap', 'condition_overlap']);
            }
        });
    }
}
