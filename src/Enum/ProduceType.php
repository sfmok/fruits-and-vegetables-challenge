<?php

declare(strict_types=1);

namespace App\Enum;

enum ProduceType: string
{
    case Fruit = 'fruit';
    case Vegetable = 'vegetable';

    public function getPlural(): string
    {
        return match ($this) {
            self::Fruit => 'fruits',
            self::Vegetable => 'vegetables',
        };
    }
}