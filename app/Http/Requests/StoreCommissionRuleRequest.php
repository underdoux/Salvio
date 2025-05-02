<?php

namespace App\Http\Requests;

use App\Models\CommissionRule;
use App\Rules\CommissionRuleConditions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class StoreCommissionRuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->can('create', CommissionRule::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0', 'max:100'],
            'conditions' => ['nullable', 'json', new CommissionRuleConditions],
            'active' => ['boolean'],
            'priority' => ['nullable', 'integer', 'min:0'],
            'effective_from' => ['nullable', 'date'],
            'effective_until' => ['nullable', 'date', 'after:effective_from'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_template' => ['boolean'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        // Handle conditions
        if ($this->has('conditions')) {
            $conditions = $this->get('conditions');
            if (is_string($conditions)) {
                $data['conditions'] = json_decode($conditions, true);
            }
        }

        // Handle active flag
        if ($this->has('active')) {
            $data['active'] = filter_var($this->get('active'), FILTER_VALIDATE_BOOLEAN);
        }

        // Handle is_template flag
        if ($this->has('is_template')) {
            $data['is_template'] = filter_var($this->get('is_template'), FILTER_VALIDATE_BOOLEAN);
        }

        if (!empty($data)) {
            $this->replace(array_merge($this->all(), $data));
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'A rule name is required.',
            'type.required' => 'Please select a commission type.',
            'type.in' => 'The commission type must be either percentage or fixed.',
            'value.required' => 'A commission value is required.',
            'value.numeric' => 'The commission value must be a number.',
            'value.min' => 'The commission value cannot be negative.',
            'value.max' => 'The commission value cannot exceed 100.',
            'conditions.json' => 'The conditions must be valid JSON.',
            'effective_from.date' => 'The effective from date must be a valid date.',
            'effective_until.date' => 'The effective until date must be a valid date.',
            'effective_until.after' => 'The effective until date must be after the effective from date.',
            'priority.integer' => 'The priority must be a whole number.',
            'priority.min' => 'The priority cannot be negative.',
            'description.max' => 'The description cannot exceed 1000 characters.',
        ];
    }
}
