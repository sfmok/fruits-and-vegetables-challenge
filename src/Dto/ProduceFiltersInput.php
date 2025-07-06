<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enum\ProduceUnit;
use App\Enum\ProduceType;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProduceFiltersInput
{
    public function __construct(
        public ?string $name = null,

        #[Assert\Positive(message: 'Minimum quantity must be positive')]
        #[Assert\LessThan(propertyPath: 'maxQuantity', message: 'Minimum quantity must be less than maximum quantity')]
        #[SerializedName('min_quantity')]
        public ?int $minQuantity = null,

        #[Assert\Positive(message: 'Maximum quantity must be positive')]
        #[Assert\GreaterThan(propertyPath: 'minQuantity', message: 'Maximum quantity must be greater than minimum quantity')]
        #[SerializedName('max_quantity')]
        public ?int $maxQuantity = null,

        #[Assert\Choice(choices: [ProduceType::Fruit->value, ProduceType::Vegetable->value], message: 'Type must be either "fruit" or "vegetable"')]
        public ?string $type = null,

        #[Assert\Choice(choices: [ProduceUnit::Gram->value, ProduceUnit::Kilogram->value], message: 'Unit must be either "g" or "kg"')]
        public ?string $unit = null,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'min_quantity' => $this->minQuantity,
            'max_quantity' => $this->maxQuantity,
            'unit' => $this->unit,
        ];
    }
}