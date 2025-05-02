<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionRuleVersion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'commission_rule_id',
        'version_number',
        'data',
        'change_reason',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'version_number' => 'integer',
    ];

    /**
     * Get the commission rule that owns the version.
     */
    public function commissionRule(): BelongsTo
    {
        return $this->belongsTo(CommissionRule::class);
    }

    /**
     * Get the user who created the version.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Compare this version with another version.
     */
    public function compareWith(CommissionRuleVersion $other): array
    {
        $differences = [];
        $currentData = $this->data;
        $otherData = $other->data;

        foreach ($currentData as $key => $value) {
            if (!isset($otherData[$key])) {
                $differences[$key] = [
                    'type' => 'added',
                    'current' => $value,
                    'other' => null,
                ];
                continue;
            }

            if ($value !== $otherData[$key]) {
                $differences[$key] = [
                    'type' => 'modified',
                    'current' => $value,
                    'other' => $otherData[$key],
                ];
            }
        }

        foreach ($otherData as $key => $value) {
            if (!isset($currentData[$key])) {
                $differences[$key] = [
                    'type' => 'removed',
                    'current' => null,
                    'other' => $value,
                ];
            }
        }

        return $differences;
    }

    /**
     * Get the version identifier.
     */
    public function getIdentifier(): string
    {
        return sprintf('v%d', $this->version_number);
    }

    /**
     * Get the version summary.
     */
    public function getSummary(): string
    {
        return sprintf(
            'Version %d created by %s on %s: %s',
            $this->version_number,
            $this->creator->name,
            $this->created_at->format('Y-m-d H:i:s'),
            $this->change_reason
        );
    }

    /**
     * Restore this version to the commission rule.
     */
    public function restore(): bool
    {
        return $this->commissionRule->update([
            'name' => $this->data['name'],
            'type' => $this->data['type'],
            'value' => $this->data['value'],
            'conditions' => $this->data['conditions'],
            'active' => $this->data['active'],
            'priority' => $this->data['priority'] ?? null,
            'effective_from' => $this->data['effective_from'] ?? null,
            'effective_until' => $this->data['effective_until'] ?? null,
            'description' => $this->data['description'] ?? null,
        ]);
    }
}
