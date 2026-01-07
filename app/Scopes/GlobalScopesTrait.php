<?php

namespace App\Scopes;

trait GlobalScopesTrait
{
    public function scopeWhereKeyword($query, $filterValues)
    {
        if (isset($filterValues['keyword']) and $filterValues['keyword'] != '') {
            return $query->where('name', 'LIKE', '%' . $filterValues['keyword'] . '%');
        }
    }

    public function scopeWhereName($query, $filterValues)
    {
        if (isset($filterValues['name']) and $filterValues['name'] != '') {
            return $query->where('name', 'LIKE', '%' . $filterValues['name'] . '%');
        }
    }

    public function scopeWhereId($query, array $filterValues, string $field, string $filterValueKey)
    {

        if (isset($filterValues[$filterValueKey])) {

            if (is_array($filterValues[$filterValueKey]) and count($filterValues[$filterValueKey]) > 0) {

                return $query->whereIn($field, $filterValues[$filterValueKey]);
            }

            if (is_array($filterValues[$filterValueKey]) and count($filterValues[$filterValueKey]) == 0) {
                return;
            }

            if ($filterValues[$filterValueKey] != '') {
                return $query->where($field, $filterValues[$filterValueKey]);
            }
        }
    }

    public function scopeSortAndOrderBy($query, $filterValues)
    {

        if (!isset($filterValues['sort'])) {
            return;
        }

        if ($filterValues['sort'] == 'random') {
            return $query->inRandomOrder();
        }

        return $query->orderBy($filterValues['sort'], $filterValues['order']);
    }

    public function scopeCustomPaginateOrLimit($query, $filterValues)
    {

        if (isset($filterValues['limit']) and $filterValues['limit'] > 0) {
            return $query->take($filterValues['limit'])->get();
        }

        if (isset($filterValues['page']) and $filterValues['page']) {
            if (!isset($filterValues['per_page'])) {
                $filterValues['per_page'] = 50;
            }

            return $query->paginate($filterValues['per_page'], '*', 'page', $filterValues['page']);
        }

        return $query->get(); // Always paginate!
    }

    public function scopeWhereGroupBy($query, array $filterValues)
    {
        if (isset($filterValues['group_by']) and is_array($filterValues['group_by'])) {
            foreach ($filterValues['group_by'] as $groupBy) {
                $query->groupBy($groupBy);
            }
        }

        return $query;
    }
}
