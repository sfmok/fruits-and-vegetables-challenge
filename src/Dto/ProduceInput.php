<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enum\ProduceType;
use App\Enum\ProduceUnit;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProduceInput
{
    public function __construct(
        #[Assert\NotBlank(message: 'Id is required', allowNull: true)]
        #[Assert\Type(type: 'integer', message: 'Id must be an integer')]
        public ?int $id,

        #[Assert\NotBlank(message: 'Name is required')]
        public string $name,

        #[Assert\NotBlank(message: 'Quantity is required')]
        #[Assert\Positive(message: 'Quantity must be positive')]
        #[Assert\Type(type: 'float', message: 'Quantity must be a number')]
        public float $quantity,

        #[Assert\NotBlank(message: 'Unit is required')]
        #[Assert\Choice(choices: [ProduceUnit::Gram->value, ProduceUnit::Kilogram->value], message: 'Unit must be either "g" or "kg"')]
        public string $unit,

        #[Assert\NotBlank(message: 'Type is required')]
        #[Assert\Choice(choices: [ProduceType::Fruit->value, ProduceType::Vegetable->value], message: 'Type must be either "fruit" or "vegetable"')]
        public string $type,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'type' => $this->type,
        ];
    }
}