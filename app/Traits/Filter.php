<?php



namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filter
{
    public function scopeFilter(Builder $query, $request): Builder
    {
        if (isset($request['filters']) && is_array($request['filters'])) {
            foreach ($request['filters'] as $filterType => $filterValue) {
                $this->applyFilter($query, $filterType, $filterValue);
            }
        }

        return $query;
    }

    /**
     * Apply a single filter based on its configuration
     */
    protected function applyFilter(Builder $query, string $filterType, $filterValue)
    {
        // Skip if filter type is not defined in the model
        if (! isset($this->filters[$filterType])) {
            return;
        }

        $filterConfig = $this->filters[$filterType];

        // Get field name, filter method, and relation
        $field = is_array($filterConfig) ? $filterConfig['field'] : $filterConfig;
        $method = is_array($filterConfig) ? ($filterConfig['method'] ?? 'range') : 'range';
        $relation = is_array($filterConfig) ? ($filterConfig['relation'] ?? null) : null;

        // If a relation is defined, use whereHas, otherwise use direct filtering
        if ($relation) {
            $this->applyRelationFilter($query, $relation, $field, $method, $filterValue);
        } else {
            $this->applyDirectFilter($query, $method, $field, $filterValue);
        }
    }

    /**
     * Apply a filter directly to a field
     */
    protected function applyDirectFilter(Builder $query, string $method, string $field, $filterValue)
    {
        switch ($method) {
            case 'range':
                $this->applyRangeFilter($query, $field, $filterValue);
                break;
            case 'search':
                $this->applySearchFilter($query, $field, $filterValue);
                break;
            case 'exact':
                $this->applyExactFilter($query, $field, $filterValue);
                break;
        }
    }

    /**
     * Apply a filter through a relation
     */
    protected function applyRelationFilter(Builder $query, string $relation, string $field, string $method, $filterValue)
    {
        // Extract the actual field name from the relation.field format
        $fieldParts = explode('.', $field);
        $actualField = end($fieldParts);

        $query->whereHas($relation, function (Builder $query) use ($method, $actualField, $filterValue): void {
            $this->applyDirectFilter($query, $method, $actualField, $filterValue);
        });
    }

    /**
     * Apply a range filter with min/max values
     */
    protected function applyRangeFilter(Builder $query, string $field, $filterValue)
    {
        // Validate filter value has min and max
        if (! is_array($filterValue) || ! isset($filterValue['min']) || ! isset($filterValue['max'])) {
            return;
        }

        $min = $filterValue['min'];
        $max = $filterValue['max'];

        // Apply regular field filter
        $query->whereBetween($field, [$min, $max]);
    }

    /**
     * Apply a search filter with a text value
     */
    protected function applySearchFilter(Builder $query, string $field, $filterValue)
    {
        if (! is_string($filterValue) || ($filterValue === '' || $filterValue === '0')) {
            return;
        }

        $query->where($field, 'like', '%'.$filterValue.'%');
    }

    /**
     * Apply an exact match filter
     */
    protected function applyExactFilter(Builder $query, string $field, $filterValue)
    {
        if (is_null($filterValue)) {
            return;
        }

        $query->where($field, '=', $filterValue);
    }
}
