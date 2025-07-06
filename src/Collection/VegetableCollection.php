<?php

declare(strict_types=1);

namespace App\Collection;

use App\Enum\ProduceType;
use App\Enum\ProduceUnit;
use App\Entity\Produce;
use App\Storage\StorageInterface;

final readonly class VegetableCollection extends AbstractCollection
{
    public function __construct(private StorageInterface $storage) {}

    public function add(int $id, Produce $produce): void
    {
        $this->storage->store(ProduceType::Vegetable, $id, $produce);
    }

    public function remove(int $id): bool
    {
        return $this->storage->remove(ProduceType::Vegetable, $id);
    }

    public function get(int $id): ?Produce
    {
        return $this->storage->find(ProduceType::Vegetable, $id);
    }

    public function list(array $filters = []): array
    {
        $vegetables = $this->storage->findAll(ProduceType::Vegetable);
        
        $filteredVegetables = $this->applyFilters($vegetables, $filters);

        $unit = ProduceUnit::tryFrom($filters['unit'] ?? '') ?? ProduceUnit::Gram;
        
        return array_map(fn(Produce $vegetable) => $vegetable->toArray($unit), $filteredVegetables);
    }

    public function search(string $query, ProduceUnit $unit = ProduceUnit::Gram): array
    {
        $vegetables = $this->storage->findAll(ProduceType::Vegetable);
        
        $query = strtolower($query);

        $filteredVegetables = array_filter($vegetables, function (Produce $vegetable) use ($query) {
            return str_contains(strtolower($vegetable->getName()), $query);
        });
        
        return array_map(fn(Produce $vegetable) => $vegetable->toArray($unit), $filteredVegetables);
    }

    public function supports(ProduceType $produceType): bool
    {
        return $produceType === ProduceType::Vegetable;
    }
}