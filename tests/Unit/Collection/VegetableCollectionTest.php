<?php

declare(strict_types=1);

namespace App\Tests\Unit\Collection;

use App\Collection\VegetableCollection;
use App\Entity\Vegetable;
use App\Enum\ProduceType;
use App\Enum\ProduceUnit;

class VegetableCollectionTest extends AbstractCollectionTestCase
{
    private VegetableCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->collection = new VegetableCollection($this->storage);
    }

    public function testAdd(): void
    {
        $vegetable = $this->createVegetable(1, 'Carrot', 100.0);
        
        $this->collection->add(1, $vegetable);

        $result = $this->collection->list();
        $this->assertCount(1, $result);
        $this->assertEquals('Carrot', $result[0]['name']);
    }

    public function testRemove(): void
    {
        $vegetable = $this->createVegetable(1, 'Carrot', 100.0);
        $this->collection->add(1, $vegetable);

        $result = $this->collection->remove(1);

        $this->assertTrue($result);
        
        $items = $this->collection->list();
        $this->assertCount(0, $items);
    }

    public function testRemoveReturnsFalse(): void
    {
        $result = $this->collection->remove(999);

        $this->assertFalse($result);
    }

    public function testListWithNoFilters(): void
    {
        $vegetable1 = $this->createVegetable(1, 'Carrot', 100.0);
        $vegetable2 = $this->createVegetable(2, 'Broccoli', 150.0);
        
        $this->collection->add(1, $vegetable1);
        $this->collection->add(2, $vegetable2);

        $result = $this->collection->list();

        $this->assertCount(2, $result);
        $this->assertEquals('Carrot', $result[0]['name']);
        $this->assertEquals('Broccoli', $result[1]['name']);
    }

    public function testListWithFilters(): void
    {
        $vegetable1 = $this->createVegetable(1, 'Carrot', 100.0);
        $vegetable2 = $this->createVegetable(2, 'Broccoli', 150.0);
        $vegetable3 = $this->createVegetable(3, 'Spinach', 80.0);
        
        $this->collection->add(1, $vegetable1);
        $this->collection->add(2, $vegetable2);
        $this->collection->add(3, $vegetable3);

        $result = $this->collection->list([
            'name' => 'carrot',
            'min_quantity' => 50.0,
        ]);

        $this->assertCount(1, $result);
        $this->assertEquals('Carrot', $result[0]['name']);
    }

    public function testListWithUnitFilter(): void
    {
        $vegetable = $this->createVegetable(1, 'Carrot', 1000.0); // 1.0 kg
        $this->collection->add(1, $vegetable);

        $result = $this->collection->list(['unit' => 'kg']);

        $this->assertCount(1, $result);
        $this->assertEquals(1.0, $result[0]['quantity']);
        $this->assertEquals('kg', $result[0]['unit']);
    }

    public function testListWithInvalidUnitFilter(): void
    {
        $vegetable = $this->createVegetable(1, 'Carrot', 100.0);
        $this->collection->add(1, $vegetable);

        $result = $this->collection->list(['unit' => 'invalid']);

        $this->assertCount(1, $result);
        $this->assertEquals('g', $result[0]['unit']); // Default to grams
    }

    public function testSearch(): void
    {
        $vegetable1 = $this->createVegetable(1, 'Carrot', 100.0);
        $vegetable2 = $this->createVegetable(2, 'Broccoli', 150.0);
        $vegetable3 = $this->createVegetable(3, 'Spinach', 80.0);
        
        $this->collection->add(1, $vegetable1);
        $this->collection->add(2, $vegetable2);
        $this->collection->add(3, $vegetable3);

        $result = $this->collection->search('carrot');

        $this->assertCount(1, $result);
        $this->assertEquals('Carrot', $result[0]['name']);
    }

    public function testSearchCaseInsensitive(): void
    {
        $vegetable1 = $this->createVegetable(1, 'CARROT', 100.0);
        $vegetable2 = $this->createVegetable(2, 'Broccoli', 150.0);
        
        $this->collection->add(1, $vegetable1);
        $this->collection->add(2, $vegetable2);

        $result = $this->collection->search('carrot');

        $this->assertCount(1, $result);
        $this->assertEquals('CARROT', $result[0]['name']);
    }

    public function testSearchWithUnit(): void
    {
        $vegetable = $this->createVegetable(1, 'Carrot', 1000.0); // 1.0 kg
        $this->collection->add(1, $vegetable);

        $result = $this->collection->search('carrot', ProduceUnit::Kilogram);

        $this->assertCount(1, $result);
        $this->assertEquals(1.0, $result[0]['quantity']);
        $this->assertEquals('kg', $result[0]['unit']);
    }

    public function testSearchWithEmptyQuery(): void
    {
        $vegetable1 = $this->createVegetable(1, 'Carrot', 100.0);
        $vegetable2 = $this->createVegetable(2, 'Broccoli', 150.0);
        
        $this->collection->add(1, $vegetable1);
        $this->collection->add(2, $vegetable2);

        $result = $this->collection->search('');

        $this->assertCount(2, $result);
    }

    public function testSupportsVegetableType(): void
    {
        $result = $this->collection->supports(ProduceType::Vegetable);
        $this->assertTrue($result);
    }

    public function testSupportsFruitType(): void
    {
        $result = $this->collection->supports(ProduceType::Fruit);
        $this->assertFalse($result);
    }

    private function createVegetable(int $id, string $name, float $quantityInGrams): Vegetable
    {
        return (new Vegetable())->setId($id)->setName($name)->setQuantityInGrams($quantityInGrams);
    }
} 