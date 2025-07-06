<?php

declare(strict_types=1);

namespace App\Collection;

use App\Enum\ProduceType;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class CollectionResolver
{
    /** @param array<CollectionInterface> $collections */
    public function __construct(#[AutowireIterator('produce.collections')] private iterable $collections)
    {
    }

    public function resolve(ProduceType $produceType): CollectionInterface
    {
        foreach ($this->collections as $collection) {
            if ($collection->supports($produceType)) {
                return $collection;
            }
        }

        throw new InvalidArgumentException('No collection found.');
    }
}