<?php

namespace App\Http\Requests;

use App\Models\CommissionRule;
use App\Services\CommissionRuleDependencyService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CommissionRuleDependencyRequest extends FormRequest
{
    protected CommissionRuleDependencyService $dependencyService;

    public function __construct(CommissionRuleDependencyService $dependencyService)
    {
        parent::__construct();
        $this->dependencyService = $dependencyService;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $commissionRule = $this->route('commissionRule');
        return Auth::user()->can('update', $commissionRule);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $commissionRule = $this->route('commissionRule');

        return [
            'depends_on_rule_id' => [
                'required',
                'exists:commission_rules,id',
                Rule::notIn([$commissionRule->id]),
                function ($attribute, $value, $fail) use ($commissionRule) {
                    if ($this->dependencyService->hasCircularDependency($commissionRule, $value)) {
                        $fail('This dependency would create a circular reference.');
                    }
                },
            ],
            'dependency_type' => [
                'required',
                'string',
                Rule::in(['requires', 'conflicts', 'overrides']),
            ],
            'reason' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'depends_on_rule_id.required' => 'Please select a rule to depend on.',
            'depends_on_rule_id.exists' => 'The selected rule does not exist.',
            'depends_on_rule_id.not_in' => 'A rule cannot depend on itself.',
            'dependency_type.required' => 'Please select a dependency type.',
            'dependency_type.in' => 'Invalid dependency type selected.',
            'reason.required' => 'Please provide a reason for this dependency.',
            'reason.max' => 'The reason cannot exceed 255 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = [
            'commission_rule_id' => $this->route('commissionRule')->id,
            'depends_on_rule_id' => $this->input('depends_on_rule_id'),
            'dependency_type' => $this->input('dependency_type'),
            'reason' => $this->input('reason'),
        ];

        $this->merge($data);

        // Check for date overlaps if dependency type is 'requires'
        if ($this->input('dependency_type') === 'requires') {
            $commissionRule = $this->route('commissionRule');
            $dependsOnRule = CommissionRule::findOrFail($this->input('depends_on_rule_id'));

            if (!$this->dependencyService->hasDateOverlap($commissionRule, $dependsOnRule)) {
                $this->merge([
                    'effective_from' => $dependsOnRule->effective_from,
                    'effective_until' => $dependsOnRule->effective_until,
                ]);
            }
        }

        // Check for conflicts if dependency type is 'conflicts'
        if ($this->input('dependency_type') === 'conflicts') {
            $commissionRule = $this->route('commissionRule');
            $dependsOnRule = CommissionRule::findOrFail($this->input('depends_on_rule_id'));

            $this->merge([
                'conflict_details' => $this->dependencyService->getConflictDetails($commissionRule, $dependsOnRule),
            ]);
        }

        // Check for overrides if dependency type is 'overrides'
        if ($this->input('dependency_type') === 'overrides') {
            $commissionRule = $this->route('commissionRule');
            $dependsOnRule = CommissionRule::findOrFail($this->input('depends_on_rule_id'));

            $this->merge([
                'override_details' => $this->dependencyService->getOverrideDetails($commissionRule, $dependsOnRule),
            ]);
        }
    }
}
