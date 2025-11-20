<?php

namespace App\Contracts\DTOs;

interface UserDTO
{
    public function getId(): ?int;
    public function getName(): string;
    public function getEmail(): string;
    public function toArray(): array;
}

