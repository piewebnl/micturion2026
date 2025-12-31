<?php

namespace App\Traits\Forms;

use App\Helpers\SearchFormHelper;

trait SearchForm
{
    public function toggleSort()
    {
        // Always revert to default sorting
        $key = array_search($this->filterValues['sort'], array_column($this->searchFormData['sort'], 'order'));
        $this->filterValues['order'] = $this->searchFormData['sort'][$key]['order'];

        $this->search();
    }

    public function toggleOrder()
    {
        if ($this->filterValues['order'] == 'asc') {
            $this->filterValues['order'] = 'desc';
        } else {
            $this->filterValues['order'] = 'asc';
        }
        $this->setOrderIcon();
        $this->search();
    }

    public function toggleShowFilter()
    {
        if ($this->filterValues['show_filter'] == false) {
            $this->filterValues['show_filter'] = true;
        } else {
            $this->filterValues['show_filter'] = false;
        }
        // $this->setOrderIcon();
        // $this->search();
    }

    public function select(string $field, string $value)
    {
        if (!isset($this->filterValues[$field])) {
            $this->filterValues[$field] = [];
        }

        if (!is_array($this->filterValues[$field])) {
            $this->filterValues[$field] = [];
        }

        if (in_array($value, $this->filterValues[$field])) {
            $this->filterValues[$field] = array_diff($this->filterValues[$field], [$value]);
        } else {
            $this->filterValues[$field][] = $value;
        }

        $this->search();
    }

    public function setOrderIcon()
    {

        if ($this->filterValues['order'] == 'asc') {
            $this->searchFormData['order_toggle_icon'] = 'up';
        } else {
            $this->searchFormData['order_toggle_icon'] = 'down';
        }
    }

    public function uncheckAll(string $field)
    {
        $this->filterValues[$field] = [];
        $this->search();
    }

    public function checkAll(string $field, array $ignore = [])
    {

        $this->filterValues[$field] = SearchFormHelper::checkAll($this->searchFormData[$field], $ignore);
        $this->search();
    }

    public function clear()
    {
        $this->beenFiltered = false;

        foreach ($this->defaultFilterValues as $key => $value) {
            $this->filterValues[$key] = $value;
        }

        $this->search();
    }

    private function skipFilterField(string $field)
    {

        foreach ($this->filtersUsed as $filter) {
            if ($filter['field'] == $field && isset($filter['skip']) && $filter['skip'] == true) {

                return true;
            }
        }

        return false;
    }

    // Show a clear button if not defaults and not skipped
    public function checkBeenFiltered()
    {
        foreach ($this->filterValues as $key => $value) {

            if (!$this->skipFilterField($key) && $value != $this->defaultFilterValues[$key]) {
                $this->beenFiltered = true;

                // $this->filterValues['page'] = 1; ??
                return;
            }
        }
        $this->beenFiltered = false;
    }

    public function countFiltersUsed()
    {
        if (!isset($this->filtersUsed)) {
            return;
        }

        $this->countFiltersUsed = 0;
        $this->woof = [];

        foreach ($this->filtersUsed as $filter) {

            $field = $filter['field'];

            if (!$this->skipFilterField($field)) {
                // Compare to init
                if ($this->filterValues[$field] == $this->defaultFilterValues[$field]) {
                } else {
                    $this->countFiltersUsed++;
                    $this->woof[] = $field;
                }
            }
        }
    }
}
