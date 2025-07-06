<?php

namespace App\Service;

use App\Enum\ProduceType;
use App\Dto\ProduceInput;
use App\Collection\CollectionResolver;
use App\Collection\CollectionInterface;
use App\Enum\ProduceUnit;
use App\Factory\ProduceFactory;
use App\Exception\ProduceNotFoundException;

final readonly class CollectionService
{
    public function __construct(private CollectionResolver $collectionResolver, private ProduceFactory $produceFactory) {}

    public function addToCollection(ProduceInput $produceDto): array
    {
        $produceType = ProduceType::from($produceDto->type);

        $collection = $this->resolveCollection($produceType);

        $produce = $this->produceFactory->createInstance($produceType, $produceDto->toArray());
        
        $collection->add($produce->getId(), $produce);

        return $collection->list();
    }

    public function removeFromCollection(ProduceType $produceType, int $id): void
    {
        $collection = $this->resolveCollection($produceType);
        
        if (! $collection->get($id)) {
            throw new ProduceNotFoundException($id);
        }
        
        $collection->remove($id);
    }

    public function getCollection(?ProduceType $produceType = null, array $filters = []): array
    {
        if (!$produceType) {
            foreach (ProduceType::cases() as $produceType) {
                $collection = $this->resolveCollection($produceType);
                $data[$produceType->getPlural()] = $collection->list($filters);
            }

            return $data;
        }

        $collection = $this->resolveCollection($produceType);
        $data[$produceType->getPlural()] = $collection->list($filters);

        return $data;
    }

    public function searchProduces(ProduceType $produceType, string $query, ProduceUnit $unit = ProduceUnit::Gram): array
    {
        $collection = $this->resolveCollection($produceType);
        
        return $collection->search($query, $unit);
    }


    private function resolveCollection(ProduceType $produceType): CollectionInterface
    {
        return $this->collectionResolver->resolve($produceType);
    }
}