<?php

namespace App\Support\Table;

use Illuminate\Foundation\Http\FormRequest;

abstract class TableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge($this->tableRules(), $this->customRules());
    }

    /**
     * Table-specific rules (pagination, sorting, filtering)
     */
    protected function tableRules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['sometimes', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'string', 'in:asc,desc'],
            'search' => ['sometimes', 'string', 'max:255'],
            'filters' => ['sometimes', 'array'],
            'filters.*' => ['sometimes'],
            'date_from' => ['sometimes', 'date'],
            'date_to' => ['sometimes', 'date', 'after_or_equal:date_from'],
        ];
    }

    /**
     * Custom rules for specific request
     */
    protected function customRules(): array
    {
        return [];
    }

    /**
     * Get pagination parameters
     */
    public function getPagination(): array
    {
        return [
            'page' => $this->integer('page', 1),
            'per_page' => $this->integer('per_page', 15),
        ];
    }

    /**
     * Get sorting parameters
     */
    public function getSorting(): array
    {
        return [
            'sort_by' => $this->string('sort_by', 'id'),
            'sort_order' => $this->string('sort_order', 'desc'),
        ];
    }

    /**
     * Get search term
     */
    public function getSearch(): ?string
    {
        return $this->string('search');
    }

    /**
     * Get filters
     */
    public function getFilters(): array
    {
        $filters = $this->input('filters', []);
        return is_array($filters) ? $filters : [];
    }

    /**
     * Get array input (compatibility method for Laravel versions that don't have array() method)
     */
    public function array(string $key, array $default = []): array
    {
        $value = $this->input($key, $default);
        return is_array($value) ? $value : $default;
    }

    /**
     * Get date range
     */
    public function getDateRange(): ?array
    {
        $from = $this->date('date_from');
        $to = $this->date('date_to');

        if (!$from && !$to) {
            return null;
        }

        return [
            'from' => $from,
            'to' => $to,
        ];
    }
}

