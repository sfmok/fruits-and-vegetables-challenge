<?php

declare(strict_types=1);

namespace App\Storage;

use App\Enum\ProduceType;
use App\Entity\Produce;

interface StorageInterface
{
    public function store(ProduceType $produceType, int $id, Produce $produce): void;
    public function findAll(ProduceType $produceType): array;
    public function find(ProduceType $produceType, int $id): ?Produce;
    public function remove(ProduceType $produceType, int $id): bool;
}