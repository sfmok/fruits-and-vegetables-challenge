<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Dto\ProduceInput;
use App\Enum\ProduceType;
use App\Enum\ProduceUnit;
use App\Factory\ProduceFactory;
use App\Service\CollectionService;
use App\Collection\FruitCollection;
use App\Collection\CollectionResolver;
use App\Collection\VegetableCollection;
use App\Exception\ProduceNotFoundException;
use App\Tests\Unit\Collection\AbstractCollectionTestCase;

class CollectionServiceTest extends AbstractCollectionTestCase
{
    private CollectionService $service;
    private ProduceFactory $produceFactory;
    private FruitCollection $fruitCollection;
    private VegetableCollection $vegetableCollection;
    private CollectionResolver $collectionResolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->produceFactory = new ProduceFactory();

        $this->fruitCollection = new FruitCollection($this->storage);
        $this->vegetableCollection = new VegetableCollection($this->storage);

        $this->collectionResolver = new CollectionResolver([
            $this->fruitCollection,
            $this->vegetableCollection,
        ]);

        $this->service = new CollectionService($this->collectionResolver, $this->produceFactory);
    }

    public function testAddToCollection(): void
    {
        $produceInput = new ProduceInput(1, 'Apple', 150.0, 'g', 'fruit');

        $this->service->addToCollection($produceInput);

        $result = $this->service->getCollection(ProduceType::Fruit);

        $this->assertEquals('Apple', $result['fruits'][0]['name'] ?? null);
        $this->assertEquals(150.0, $result['fruits'][0]['quantity'] ?? null);
        $this->assertEquals('g', $result['fruits'][0]['unit'] ?? null);
    }

    public function testRemoveFromCollection(): void
    {
        $fruitInput = new ProduceInput(1, 'Apple', 150.0, 'g', 'fruit');
        $vegetableInput = new ProduceInput(1, 'Carrot', 100.0, 'g', 'vegetable');

        $this->service->addToCollection($fruitInput);
        $this->service->addToCollection($vegetableInput);

        $beforeRemove = $this->service->getCollection();
        $this->assertCount(1, $beforeRemove['fruits']);
        $this->assertCount(1, $beforeRemove['vegetables']);

        $this->service->removeFromCollection(ProduceType::Fruit, 1);

        $afterRemove = $this->service->getCollection();
        $this->assertCount(0, $afterRemove['fruits']);
        $this->assertCount(1, $afterRemove['vegetables']);
    }

    public function testGetCollectionWithSpecificType(): void
    {
        $fruitInput = new ProduceInput(1, 'Apple', 150.0, 'g', 'fruit');
        $this->service->addToCollection($fruitInput);

        $result = $this->service->getCollection(ProduceType::Fruit, ['name' => 'apple']);

        $this->assertArrayHasKey('fruits', $result);
        $this->assertArrayNotHasKey('vegetables', $result);
        $this->assertCount(1, $result['fruits']);
        $this->assertEquals('Apple', $result['fruits'][0]['name']);
    }

    public function testGetCollectionWithoutType(): void
    {
        $fruitInput = new ProduceInput(1, 'Apple', 150.0, 'g', 'fruit');
        $vegetableInput = new ProduceInput(2, 'Carrot', 100.0, 'g', 'vegetable');

        $this->service->addToCollection($fruitInput);
        $this->service->addToCollection($vegetableInput);

        $result = $this->service->getCollection(null, ['name' => 'apple']);

        $this->assertArrayHasKey('fruits', $result);
        $this->assertArrayHasKey('vegetables', $result);
        $this->assertCount(1, $result['fruits']);
        $this->assertCount(0, $result['vegetables']); // Carrot doesn't match 'apple'
    }

    public function testSearchProduces(): void
    {
        $fruitInput = new ProduceInput(1, 'Apple', 1.5, 'kg', 'fruit');
        $this->service->addToCollection($fruitInput);

        $result = $this->service->searchProduces(ProduceType::Fruit, 'apple', ProduceUnit::Kilogram);

        $this->assertCount(1, $result);
        $this->assertEquals('Apple', $result[0]['name']);
        $this->assertEquals(1.5, $result[0]['quantity']);
        $this->assertEquals('kg', $result[0]['unit']);
    }

    public function testRemoveFromCollectionThrowsExceptionWhenProduceNotFound(): void
    {
        $this->expectException(ProduceNotFoundException::class);
        $this->expectExceptionMessage('Produce with ID 999 not found.');

        $this->service->removeFromCollection(ProduceType::Fruit, 999);
    }
}
