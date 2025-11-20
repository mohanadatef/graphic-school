<?php

namespace App\Support\DTOs;

abstract class BaseDTO
{
    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): static
    {
        $dto = new static();
        
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->$key = $value;
            }
        }

        return $dto;
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * Validate DTO data
     */
    public function validate(): void
    {
        // Override in child classes for custom validation
    }
}

