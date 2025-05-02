<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class CommissionRule extends Model
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
        'value',
        'conditions',
        'active',
        'priority',
        'effective_from',
        'effective_until',
        'description',
        'is_template',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'conditions' => 'array',
        'active' => 'boolean',
        'value' => 'decimal:2',
        'is_template' => 'boolean',
        'effective_from' => 'datetime',
        'effective_until' => 'datetime',
    ];

    /**
     * Get the versions for the commission rule.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(CommissionRuleVersion::class);
    }

    /**
     * Get the dependencies for the commission rule.
     */
    public function dependencies(): HasMany
    {
        return $this->hasMany(CommissionRuleDependency::class);
    }

    /**
     * Get the dependent rules for this commission rule.
     */
    public function dependents(): HasMany
    {
        return $this->hasMany(CommissionRuleDependency::class, 'depends_on_rule_id');
    }

    /**
     * Create a new version of this commission rule.
     */
    public function createVersion(string $reason): CommissionRuleVersion
    {
        return CommissionRuleVersion::createFromRule($this, $reason);
    }

    /**
     * Create a duplicate of this commission rule.
     */
    public function duplicate(): static
    {
        $duplicate = $this->replicate();
        $duplicate->name = $this->name . ' (Copy)';
        $duplicate->active = false;
        $duplicate->save();

        // Copy conditions and other related data
        $duplicate->conditions = $this->conditions;
        $duplicate->save();

        return $duplicate;
    }

    /**
     * Save this rule as a template.
     */
    public function saveAsTemplate(string $name): static
    {
        $template = $this->replicate();
        $template->name = $name;
        $template->is_template = true;
        $template->active = false;
        $template->save();

        // Copy conditions and other related data
        $template->conditions = $this->conditions;
        $template->save();

        return $template;
    }

    /**
     * Scope a query to only include active rules.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include templates.
     */
    public function scopeTemplates(Builder $query): Builder
    {
        return $query->where('is_template', true);
    }

    /**
     * Scope a query to exclude templates.
     */
    public function scopeNotTemplates(Builder $query): Builder
    {
        return $query->where('is_template', false);
    }

    /**
     * Check if the rule has any active dependent rules.
     */
    public function hasActiveDependents(): bool
    {
        return $this->dependents()
            ->whereHas('commissionRule', function (Builder $query) {
                $query->where('active', true);
            })
            ->exists();
    }

    /**
     * Check if all dependencies are satisfied.
     */
    public function areDependenciesSatisfied(): bool
    {
        return $this->dependencies()
            ->whereHas('dependsOnRule', function (Builder $query) {
                $query->where('active', true);
            })
            ->count() === $this->dependencies()->count();
    }
}
