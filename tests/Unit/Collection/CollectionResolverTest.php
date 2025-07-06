<?php

declare(strict_types=1);

namespace App\Tests\Unit\Collection;

use App\Collection\CollectionResolver;
use App\Collection\FruitCollection;
use App\Collection\VegetableCollection;
use App\Enum\ProduceType;
use InvalidArgumentException;

class CollectionResolverTest extends AbstractCollectionTestCase
{
    private CollectionResolver $resolver;
    private FruitCollection $fruitCollection;
    private VegetableCollection $vegetableCollection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fruitCollection = new FruitCollection($this->storage);
        $this->vegetableCollection = new VegetableCollection($this->storage);
        
        $this->resolver = new CollectionResolver([
            $this->fruitCollection,
            $this->vegetableCollection,
        ]);
    }

    public function testResolveFruitType(): void
    {
        $result = $this->resolver->resolve(ProduceType::Fruit);

        $this->assertSame($this->fruitCollection, $result);
    }

    public function testResolveVegetableType(): void
    {
        $result = $this->resolver->resolve(ProduceType::Vegetable);

        $this->assertSame($this->vegetableCollection, $result);
    }

    public function testResolveWithEmptyCollections(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No collection found.');

        $resolver = new CollectionResolver([]);
        $resolver->resolve(ProduceType::Fruit);
    }
} 