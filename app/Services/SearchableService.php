<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class SearchableService
{
    private const SEARCH_PARAMS_KEY = "search";

    public function handle(Builder $builder, array $fields, array $relations = []): Builder
    {
        $queryValue = request()->query(self::SEARCH_PARAMS_KEY, '');

        if (empty($queryValue) || empty($fields)) {
            return $builder;
        }

        $builder->where(function ($query) use ($queryValue, $fields) {
            $this->applyLikeConditions($query, $queryValue, $fields);
        });

        foreach ($relations as $relation => $columns) {
            $builder->orWhereHas($relation, function ($query) use ($queryValue, $columns) {
                $this->applyLikeConditions($query, $queryValue, $columns);
            });
        }

        return $builder;
    }

    private function applyLikeConditions(Builder $builder, string $value, array $columns): void
    {
        foreach ($columns as $index => $column) {
            if ($index === 0) {
                $builder->where($column, 'LIKE', "%{$value}%");
            } else {
                $builder->orWhere($column, 'LIKE', "%{$value}%");
            }
        }
    }
}