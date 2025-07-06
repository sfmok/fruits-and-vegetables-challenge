<?php

declare(strict_types=1);

namespace App\Collection;
use App\Enum\ProduceType;
use App\Enum\ProduceUnit;
use App\Entity\Produce;
use App\Storage\StorageInterface;

final readonly class FruitCollection extends AbstractCollection
{
    public function __construct(private StorageInterface $storage) {}

    #[\Override]
    public function add(int $id, Produce $produce): void
    {
        $this->storage->store(ProduceType::Fruit, $id, $produce);
    }

    #[\Override]
    public function remove(int $id): void
    {
        $this->storage->remove(ProduceType::Fruit, $id);
    }

    #[\Override]
    public function get(int $id): ?Produce
    {
        return $this->storage->find(ProduceType::Fruit, $id);
    }

    #[\Override]
    public function list(array $filters = []): array
    {
        $fruits = $this->storage->findAll(ProduceType::Fruit);
        
        $filteredFruits = $this->applyFilters($fruits, $filters);

        $unit = ProduceUnit::tryFrom($filters['unit'] ?? '') ?? ProduceUnit::Gram;
        
        return array_map(fn(Produce $fruit) => $fruit->toArray($unit), $filteredFruits);
    }

    #[\Override]
    public function search(string $query, ProduceUnit $unit = ProduceUnit::Gram): array
    {
        $fruits = $this->storage->findAll(ProduceType::Fruit);
        
        $query = strtolower($query);

        $filteredFruits = array_filter($fruits, function (Produce $fruit) use ($query) {
            return str_contains(strtolower($fruit->getName()), $query);
        });
        
        return array_map(fn(Produce $fruit) => $fruit->toArray($unit), $filteredFruits);
    }

    #[\Override]
    public function supports(ProduceType $produceType): bool
    {
        return $produceType === ProduceType::Fruit;
    }
}