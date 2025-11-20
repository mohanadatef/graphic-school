<?php

namespace App\Support\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TableBuilder
{
    protected Builder $query;
    protected array $sortableColumns = [];
    protected array $searchableColumns = [];
    protected array $filterableColumns = [];

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * Set sortable columns
     */
    public function sortable(array $columns): self
    {
        $this->sortableColumns = $columns;
        return $this;
    }

    /**
     * Set searchable columns
     */
    public function searchable(array $columns): self
    {
        $this->searchableColumns = $columns;
        return $this;
    }

    /**
     * Set filterable columns
     */
    public function filterable(array $columns): self
    {
        $this->filterableColumns = $columns;
        return $this;
    }

    /**
     * Apply sorting
     */
    public function applySorting(?string $sortBy, ?string $sortOrder): self
    {
        if (!$sortBy || !in_array($sortBy, $this->sortableColumns)) {
            $sortBy = 'id';
        }

        $sortOrder = in_array(strtolower($sortOrder ?? 'desc'), ['asc', 'desc'])
            ? strtolower($sortOrder)
            : 'desc';

        $this->query->orderBy($sortBy, $sortOrder);

        return $this;
    }

    /**
     * Apply search
     */
    public function applySearch(?string $search): self
    {
        if (empty($search) || empty($this->searchableColumns)) {
            return $this;
        }

        $this->query->where(function ($q) use ($search) {
            foreach ($this->searchableColumns as $index => $column) {
                if ($index === 0) {
                    $q->where($column, 'like', "%{$search}%");
                } else {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            }
        });

        return $this;
    }

    /**
     * Apply filters
     */
    public function applyFilters(array $filters): self
    {
        foreach ($filters as $key => $value) {
            if (!in_array($key, $this->filterableColumns) || $value === null || $value === '') {
                continue;
            }

            // Handle different filter types
            if (is_array($value)) {
                $this->query->whereIn($key, $value);
            } elseif (is_bool($value)) {
                $this->query->where($key, $value);
            } elseif (str_contains($key, '_from')) {
                $column = str_replace('_from', '', $key);
                $this->query->where($column, '>=', $value);
            } elseif (str_contains($key, '_to')) {
                $column = str_replace('_to', '', $key);
                $this->query->where($column, '<=', $value);
            } else {
                $this->query->where($key, $value);
            }
        }

        return $this;
    }

    /**
     * Apply date range
     */
    public function applyDateRange(?string $dateFrom, ?string $dateTo, string $column = 'created_at'): self
    {
        if ($dateFrom) {
            $this->query->whereDate($column, '>=', $dateFrom);
        }

        if ($dateTo) {
            $this->query->whereDate($column, '<=', $dateTo);
        }

        return $this;
    }

    /**
     * Build and paginate
     */
    public function paginate(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return $this->query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Build and get
     */
    public function get(): Collection
    {
        return $this->query->get();
    }

    /**
     * Get the query builder
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }
}

