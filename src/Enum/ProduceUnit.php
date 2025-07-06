<?php

declare(strict_types=1);

namespace App\Enum;

enum ProduceUnit: string
{
    case Kilogram = 'kg';
    case Gram = 'g';

    public function convertToGrams(float $weight): float
    {
        return match($this) {
            self::Gram => $weight,
            self::Kilogram => $weight * 1000,
        };
    }

    public function convertFromGrams(float $weightInGrams): float
    {
        return match($this) {
            self::Gram => $weightInGrams,
            self::Kilogram => $weightInGrams / 1000,
        };
    }
}