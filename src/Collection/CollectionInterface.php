<?php

declare(strict_types=1);

namespace App\Collection;

use App\Enum\ProduceUnit;
use App\Enum\ProduceType;
use App\Entity\Produce;

interface CollectionInterface
{
    public function add(int $id, Produce $produce): void;
    public function remove(int $id): void;
    public function get(int $id): ?Produce;
    public function list(array $filters = []): array;
    public function search(string $query, ProduceUnit $unit = ProduceUnit::Gram): array;
    public function supports(ProduceType $produceType): bool;
}