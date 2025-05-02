<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class CommissionRuleConditions implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('The conditions must be a valid JSON object.');
            return;
        }

        $schema = [
            'type' => 'object',
            'properties' => [
                'minimum_order_amount' => ['type' => 'number', 'minimum' => 0],
                'maximum_order_amount' => ['type' => 'number', 'minimum' => 0],
                'product_categories' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'customer_groups' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'payment_methods' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'order_count' => [
                    'type' => 'object',
                    'properties' => [
                        'minimum' => ['type' => 'integer', 'minimum' => 0],
                        'maximum' => ['type' => 'integer', 'minimum' => 0],
                    ],
                    'additionalProperties' => false,
                ],
                'time_restrictions' => [
                    'type' => 'object',
                    'properties' => [
                        'days_of_week' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'integer',
                                'minimum' => 0,
                                'maximum' => 6,
                            ],
                        ],
                        'hours' => [
                            'type' => 'object',
                            'properties' => [
                                'start' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 23],
                                'end' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 23],
                            ],
                            'required' => ['start', 'end'],
                            'additionalProperties' => false,
                        ],
                    ],
                    'additionalProperties' => false,
                ],
            ],
            'additionalProperties' => false,
        ];

        try {
            $this->validateAgainstSchema($value, $schema);
        } catch (\Exception $e) {
            Log::error('Commission rule conditions validation failed', [
                'error' => $e->getMessage(),
                'conditions' => $value,
            ]);
            $fail($e->getMessage());
        }
    }

    /**
     * Validate data against JSON schema.
     *
     * @param array $data
     * @param array $schema
     * @throws \Exception
     */
    protected function validateAgainstSchema(array $data, array $schema): void
    {
        // Validate minimum_order_amount and maximum_order_amount relationship
        if (isset($data['minimum_order_amount'], $data['maximum_order_amount'])) {
            if ($data['minimum_order_amount'] > $data['maximum_order_amount']) {
                throw new \Exception('Minimum order amount cannot be greater than maximum order amount.');
            }
        }

        // Validate order_count minimum and maximum relationship
        if (isset($data['order_count'])) {
            $orderCount = $data['order_count'];
            if (isset($orderCount['minimum'], $orderCount['maximum']) &&
                $orderCount['minimum'] > $orderCount['maximum']) {
                throw new \Exception('Minimum order count cannot be greater than maximum order count.');
            }
        }

        // Validate time restrictions
        if (isset($data['time_restrictions'])) {
            $timeRestrictions = $data['time_restrictions'];

            // Validate days of week
            if (isset($timeRestrictions['days_of_week'])) {
                $days = $timeRestrictions['days_of_week'];
                if (!is_array($days)) {
                    throw new \Exception('Days of week must be an array.');
                }
                foreach ($days as $day) {
                    if (!is_int($day) || $day < 0 || $day > 6) {
                        throw new \Exception('Invalid day of week. Must be between 0 (Sunday) and 6 (Saturday).');
                    }
                }
            }

            // Validate hours
            if (isset($timeRestrictions['hours'])) {
                $hours = $timeRestrictions['hours'];
                if (!isset($hours['start'], $hours['end'])) {
                    throw new \Exception('Time restrictions hours must include both start and end times.');
                }
                if ($hours['start'] < 0 || $hours['start'] > 23 ||
                    $hours['end'] < 0 || $hours['end'] > 23) {
                    throw new \Exception('Hours must be between 0 and 23.');
                }
                if ($hours['start'] > $hours['end']) {
                    throw new \Exception('Start hour cannot be greater than end hour.');
                }
            }
        }

        // Validate arrays have unique values
        foreach (['product_categories', 'customer_groups', 'payment_methods'] as $arrayField) {
            if (isset($data[$arrayField])) {
                if (!is_array($data[$arrayField])) {
                    throw new \Exception("The {$arrayField} must be an array.");
                }
                $uniqueValues = array_unique($data[$arrayField]);
                if (count($uniqueValues) !== count($data[$arrayField])) {
                    throw new \Exception("The {$arrayField} cannot contain duplicate values.");
                }
            }
        }
    }
}
