<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Entity\Produce;
use App\Enum\ProduceType;

final class ProduceFactory
{
    public function createInstance(ProduceType $produceType, array $data = []): Produce
    {
        return match ($produceType) {
            ProduceType::Fruit => Fruit::createFromArray($data),
            ProduceType::Vegetable => Vegetable::createFromArray($data),
        };
    }
} 