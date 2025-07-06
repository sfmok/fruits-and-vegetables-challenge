<?php

declare(strict_types=1);

namespace App\Storage;

use App\Entity\Produce;
use App\Enum\ProduceType;
use App\Factory\ProduceFactory;

final class FileStorage implements StorageInterface
{
    private string $storageDir;

    public function __construct(private ProduceFactory $produceFactory, ?string $storageDir = null)
    {
        $this->storageDir = $storageDir ?? '/tmp/produces';
        
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0777, true);
        }
    }

    public function store(ProduceType $produceType, int $id, Produce $produce): void
    {
        $data = $this->loadData($produceType);
        $data[$id] = $produce;
        $this->saveData($produceType, $data);
    }

    public function findAll(ProduceType $produceType): array
    {
        return array_values($this->loadData($produceType));
    }

    public function find(ProduceType $produceType, int $id): ?Produce
    {
        $data = $this->loadData($produceType);
        
        return $data[$id] ?? null;
    }

    public function remove(ProduceType $produceType, int $id): bool
    {
        $data = $this->loadData($produceType);
        
        if (isset($data[$id])) {
            unset($data[$id]);
            $this->saveData($produceType, $data);
            return true;
        }
        
        return false;
    }

    private function loadData(ProduceType $produceType): array
    {
        $filePath = $this->getFilePath($produceType);
        
        if (!file_exists($filePath)) {
            return [];
        }

        $data = json_decode(file_get_contents($filePath), true) ?? [];
        
        return array_map(fn(array $item) => $this->produceFactory->createInstance($produceType, $item), $data);
    }

    private function saveData(ProduceType $produceType, array $data): void
    {
        $filePath = $this->getFilePath($produceType);

        $saveData = array_map(fn(Produce $produce) => $produce->toArray(), $data);

        file_put_contents($filePath, json_encode($saveData));
    }

    private function getFilePath(ProduceType $produceType): string
    {
        return $this->storageDir . '/' . $produceType->value . '.json';
    }
}