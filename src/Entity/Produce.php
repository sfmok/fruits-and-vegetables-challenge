<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\ProduceUnit;

abstract class Produce
{
    protected int $id;
    protected string $name;
    protected float $quantityInGrams;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getQuantityInGrams(): float
    {
        return $this->quantityInGrams;
    }

    public function setQuantityInGrams(float $quantityInGrams): static
    {
        $this->quantityInGrams = $quantityInGrams;
        
        return $this;
    }

    public function getQuantityInUnit(ProduceUnit $unit): float
    {
        return $unit->convertFromGrams($this->quantityInGrams);
    }

    public function toArray(ProduceUnit $unit = ProduceUnit::Gram): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'quantity' => $this->getQuantityInUnit($unit),
            'unit' => $unit->value,
        ];
    }

    public static function createFromArray(array $data): static
    {
        return (new static())
            ->setId($data['id'] ?? time())
            ->setName($data['name'])
            ->setQuantityInGrams(ProduceUnit::from($data['unit'])->convertToGrams($data['quantity']));
    }
}