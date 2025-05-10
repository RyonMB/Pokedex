<?php

namespace App\Traits;

use ReflectionClass;
use RuntimeException;

trait FilterRequestTrait
{
    /**
     * Add filter validation rules based on the model's filter definitions
     *
     * @return array Validation rules for the filters
     *
     * @throws RuntimeException If modelClass property is not defined
     */
    protected function getFilterRules(): array
    {
        $rules = [
            'filters' => 'sometimes|array',
        ];

        // Check if modelClass property exists
        if (! property_exists($this, 'modelClass')) {
            throw new RuntimeException('$modelClass property must be defined in the request class using FilterRequestTrait');
        }

        $modelClass = $this->modelClass;

        // Instantiate the model to access its filter definitions
        $model = new $modelClass;

        // Get filters property using reflection to access protected/private properties
        $reflection = new ReflectionClass($model);

        if (! $reflection->hasProperty('filters')) {
            return $rules;
        }

        $property = $reflection->getProperty('filters');
        $filters = $property->getValue($model);

        if (empty($filters)) {
            return $rules;
        }

        // Add validation rules for each filter type
        foreach ($filters as $filterType => $config) {
            $method = is_array($config) ? ($config['method'] ?? 'range') : 'range';

            switch ($method) {
                case 'range':
                    $rules["filters.{$filterType}"] = 'sometimes|array';
                    $rules["filters.{$filterType}.min"] = 'required_with:filters.'.$filterType.'|numeric';
                    $rules["filters.{$filterType}.max"] = 'required_with:filters.'.$filterType.'|numeric|gte:filters.'.$filterType.'.min';
                    break;

                case 'search':
                    $rules["filters.{$filterType}"] = 'sometimes|string|max:255';
                    break;

                case 'exact':
                    // Use validation rule from model if specified, otherwise default to string
                    if (is_array($config) && isset($config['validation'])) {
                        $rules["filters.{$filterType}"] = 'sometimes|'.$config['validation'];
                    } else {
                        $rules["filters.{$filterType}"] = 'sometimes|string';
                    }
                    break;
            }
        }

        return $rules;
    }

    /**
     * Get validation messages for filter rules
     */
    protected function getFilterMessages(): array
    {
        return [
            'filters.*.min.required_with' => 'The minimum value is required for range filters.',
            'filters.*.max.required_with' => 'The maximum value is required for range filters.',
            'filters.*.max.gte' => 'The maximum value must be greater than or equal to the minimum value.',
        ];
    }

    /**
     * Get human-readable attribute names for filter fields
     */
    protected function getFilterAttributes(): array
    {
        $attributes = [];

        foreach ($this->rules() as $key => $value) {
            if (mb_strpos((string) $key, 'filters.') === 0) {
                $parts = explode('.', (string) $key);

                if (count($parts) === 2) {
                    // For the main filter field (e.g., filters.name)
                    $filterName = $parts[1];
                    $humanized = ucwords(str_replace('_', ' ', $filterName));
                    $attributes[$key] = $humanized;
                } elseif (count($parts) === 3) {
                    // For nested fields (e.g., filters.weight.min)
                    $filterName = $parts[1];
                    $subField = $parts[2];
                    $humanized = ucwords(str_replace('_', ' ', $filterName));
                    $attributes[$key] = "$humanized $subField value";
                }
            }
        }

        return $attributes;
    }
}
