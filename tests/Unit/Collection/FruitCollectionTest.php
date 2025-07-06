<?php

declare(strict_types=1);

namespace App\Tests\Unit\Collection;

use App\Collection\FruitCollection;
use App\Entity\Fruit;
use App\Enum\ProduceType;
use App\Enum\ProduceUnit;

class FruitCollectionTest extends AbstractCollectionTestCase
{
    private FruitCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->collection = new FruitCollection($this->storage);
    }

    public function testAdd(): void
    {
        $fruit = $this->createFruit(1, 'Apple', 150.0);

        $this->collection->add(1, $fruit);

        $result = $this->collection->list();
        $this->assertCount(1, $result);
        $this->assertEquals('Apple', $result[0]['name']);
    }

    public function testRemove(): void
    {
        $fruit = $this->createFruit(1, 'Apple', 150.0);
        $this->collection->add(1, $fruit);

        $this->collection->remove(1);

        $items = $this->collection->list();
        $this->assertCount(0, $items);
    }

    public function testListWithNoFilters(): void
    {
        $fruit1 = $this->createFruit(1, 'Apple', 150.0);
        $fruit2 = $this->createFruit(2, 'Banana', 200.0);

        $this->collection->add(1, $fruit1);
        $this->collection->add(2, $fruit2);

        $result = $this->collection->list();

        $this->assertCount(2, $result);
        $this->assertEquals('Apple', $result[0]['name']);
        $this->assertEquals('Banana', $result[1]['name']);
    }

    public function testListWithFilters(): void
    {
        $fruit1 = $this->createFruit(1, 'Apple', 150.0);
        $fruit2 = $this->createFruit(2, 'Banana', 200.0);
        $fruit3 = $this->createFruit(3, 'Orange', 180.0);

        $this->collection->add(1, $fruit1);
        $this->collection->add(2, $fruit2);
        $this->collection->add(3, $fruit3);

        $result = $this->collection->list([
            'name' => 'apple',
            'min_quantity' => 100.0,
        ]);

        $this->assertCount(1, $result);
        $this->assertEquals('Apple', $result[0]['name']);
    }

    public function testListWithUnitFilter(): void
    {
        $fruit = $this->createFruit(1, 'Apple', 1500.0); // 1.5 kg
        $this->collection->add(1, $fruit);

        $result = $this->collection->list(['unit' => 'kg']);

        $this->assertCount(1, $result);
        $this->assertEquals(1.5, $result[0]['quantity']);
        $this->assertEquals('kg', $result[0]['unit']);
    }

    public function testListWithInvalidUnitFilter(): void
    {
        $fruit = $this->createFruit(1, 'Apple', 150.0);
        $this->collection->add(1, $fruit);

        $result = $this->collection->list(['unit' => 'invalid']);

        $this->assertCount(1, $result);
        $this->assertEquals('g', $result[0]['unit']); // Default to grams
    }

    public function testSearch(): void
    {
        $fruit1 = $this->createFruit(1, 'Apple', 150.0);
        $fruit2 = $this->createFruit(2, 'Banana', 200.0);
        $fruit3 = $this->createFruit(3, 'Orange', 180.0);

        $this->collection->add(1, $fruit1);
        $this->collection->add(2, $fruit2);
        $this->collection->add(3, $fruit3);

        $result = $this->collection->search('apple');

        $this->assertCount(1, $result);
        $this->assertEquals('Apple', $result[0]['name']);
    }

    public function testSearchCaseInsensitive(): void
    {
        $fruit1 = $this->createFruit(1, 'APPLE', 150.0);
        $fruit2 = $this->createFruit(2, 'Banana', 200.0);

        $this->collection->add(1, $fruit1);
        $this->collection->add(2, $fruit2);

        $result = $this->collection->search('apple');

        $this->assertCount(1, $result);
        $this->assertEquals('APPLE', $result[0]['name']);
    }

    public function testSearchWithUnit(): void
    {
        $fruit = $this->createFruit(1, 'Apple', 1500.0); // 1.5 kg
        $this->collection->add(1, $fruit);

        $result = $this->collection->search('apple', ProduceUnit::Kilogram);

        $this->assertCount(1, $result);
        $this->assertEquals(1.5, $result[0]['quantity']);
        $this->assertEquals('kg', $result[0]['unit']);
    }

    public function testSearchWithEmptyQuery(): void
    {
        $fruit1 = $this->createFruit(1, 'Apple', 150.0);
        $fruit2 = $this->createFruit(2, 'Banana', 200.0);

        $this->collection->add(1, $fruit1);
        $this->collection->add(2, $fruit2);

        $result = $this->collection->search('');

        $this->assertCount(2, $result);
    }

    public function testSupportsFruitType(): void
    {
        $result = $this->collection->supports(ProduceType::Fruit);
        $this->assertTrue($result);
    }

    public function testSupportsVegetableType(): void
    {
        $result = $this->collection->supports(ProduceType::Vegetable);
        $this->assertFalse($result);
    }

    private function createFruit(int $id, string $name, float $quantityInGrams): Fruit
    {
        return (new Fruit())->setId($id)->setName($name)->setQuantityInGrams($quantityInGrams);
    }
}
