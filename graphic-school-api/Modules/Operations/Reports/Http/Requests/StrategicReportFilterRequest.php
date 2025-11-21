<?php

namespace Modules\Operations\Reports\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StrategicReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Protected by middleware
    }

    public function rules(): array
    {
        return [
            // Date range filters
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'from_date' => ['sometimes', 'date'],
            'to_date' => ['sometimes', 'date', 'after_or_equal:from_date'],
            
            // Period filter (for performance report)
            'period' => ['sometimes', 'string', 'in:day,week,month,quarter,year'],
            
            // Forecast months
            'months' => ['sometimes', 'integer', 'min:1', 'max:24'],
            
            // Category filter
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            
            // Course filter
            'course_id' => ['sometimes', 'integer', 'exists:courses,id'],
            
            // Instructor filter
            'instructor_id' => ['sometimes', 'integer', 'exists:users,id'],
            
            // Status filter
            'status' => ['sometimes', 'string'],
        ];
    }

    /**
     * Get validated filters
     */
    public function getFilters(): array
    {
        $filters = $this->validated();
        
        // Normalize date fields
        if (isset($filters['from_date']) && !isset($filters['start_date'])) {
            $filters['start_date'] = $filters['from_date'];
        }
        if (isset($filters['to_date']) && !isset($filters['end_date'])) {
            $filters['end_date'] = $filters['to_date'];
        }
        
        // Remove empty values
        return array_filter($filters, fn($value) => $value !== null && $value !== '');
    }
}

