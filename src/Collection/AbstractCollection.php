<?php

declare(strict_types=1);

namespace App\Collection;

use App\Entity\Produce;

abstract readonly class AbstractCollection implements CollectionInterface
{
    protected function applyFilters(array $produces, array $filters): array
    {
        return array_values(array_filter($produces, function (Produce $produce) use ($filters) {
            foreach ($filters as $field => $value) {
                if (!$value || in_array($field, ['unit', 'type'], true)) {
                    continue;
                }

                $shouldReturnFalse = match ($field) {
                    'name' => strtolower($produce->getName()) !== strtolower($value),
                    'min_quantity' => $produce->getQuantityInGrams() < $value,
                    'max_quantity' => $produce->getQuantityInGrams() > $value,
                    default => false,
                };
                
                if ($shouldReturnFalse) {
                    return false;
                }
            }
            return true;
        }));
    }
}