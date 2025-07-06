<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Fruit;
use App\Enum\ProduceUnit;
use PHPUnit\Framework\TestCase;

class ProduceTest extends TestCase
{
    private Fruit $produce;

    protected function setUp(): void
    {
        $this->produce = new Fruit();
    }

    public function testGetId(): void
    {
        $this->produce->setId(1);
        $this->assertEquals(1, $this->produce->getId());
    }

    public function testSetId(): void
    {
        $result = $this->produce->setId(1);
        $this->assertSame($this->produce, $result);
        $this->assertEquals(1, $this->produce->getId());
    }

    public function testGetName(): void
    {
        $this->produce->setName('Apple');
        $this->assertEquals('Apple', $this->produce->getName());
    }

    public function testSetName(): void
    {
        $result = $this->produce->setName('Apple');
        $this->assertSame($this->produce, $result);
        $this->assertEquals('Apple', $this->produce->getName());
    }

    public function testGetQuantityInGrams(): void
    {
        $this->produce->setQuantityInGrams(150.0);
        $this->assertEquals(150.0, $this->produce->getQuantityInGrams());
    }

    public function testSetQuantityInGrams(): void
    {
        $result = $this->produce->setQuantityInGrams(150.0);
        $this->assertSame($this->produce, $result);
        $this->assertEquals(150.0, $this->produce->getQuantityInGrams());
    }

    public function testGetQuantityInUnitWithGrams(): void
    {
        $this->produce->setQuantityInGrams(150.0);
        $quantity = $this->produce->getQuantityInUnit(ProduceUnit::Gram);
        $this->assertEquals(150.0, $quantity);
    }

    public function testGetQuantityInUnitWithKilograms(): void
    {
        $this->produce->setQuantityInGrams(1500.0);
        $quantity = $this->produce->getQuantityInUnit(ProduceUnit::Kilogram);
        $this->assertEquals(1.5, $quantity);
    }

    public function testToArrayWithDefaultUnit(): void
    {
        $this->produce
            ->setId(1)
            ->setName('Apple')
            ->setQuantityInGrams(150.0);

        $result = $this->produce->toArray();

        $this->assertEquals([
            'id' => 1,
            'name' => 'Apple',
            'quantity' => 150.0,
            'unit' => 'g',
        ], $result);
    }

    public function testToArrayWithKilogramUnit(): void
    {
        $this->produce
            ->setId(1)
            ->setName('Apple')
            ->setQuantityInGrams(1500.0);

        $result = $this->produce->toArray(ProduceUnit::Kilogram);

        $this->assertEquals([
            'id' => 1,
            'name' => 'Apple',
            'quantity' => 1.5,
            'unit' => 'kg',
        ], $result);
    }

    public function testCreateFromArray(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Apple',
            'quantity' => 150.0,
            'unit' => 'g',
        ];

        $result = Fruit::createFromArray($data);

        $this->assertInstanceOf(Fruit::class, $result);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('Apple', $result->getName());
        $this->assertEquals(150.0, $result->getQuantityInGrams());
    }

    public function testCreateFromArrayWithKilogramUnit(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Apple',
            'quantity' => 1.5,
            'unit' => 'kg',
        ];

        $result = Fruit::createFromArray($data);

        $this->assertInstanceOf(Fruit::class, $result);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('Apple', $result->getName());
        $this->assertEquals(1500.0, $result->getQuantityInGrams());
    }

    public function testCreateFromArrayWithoutId(): void
    {
        $data = [
            'name' => 'Apple',
            'quantity' => 150.0,
            'unit' => 'g',
        ];

        $result = Fruit::createFromArray($data);

        $this->assertInstanceOf(Fruit::class, $result);
        $this->assertGreaterThan(0, $result->getId()); // Should use time() as fallback
        $this->assertEquals('Apple', $result->getName());
        $this->assertEquals(150.0, $result->getQuantityInGrams());
    }

    public function testCreateFromArrayWithNullId(): void
    {
        $data = [
            'id' => null,
            'name' => 'Apple',
            'quantity' => 150.0,
            'unit' => 'g',
        ];

        $result = Fruit::createFromArray($data);

        $this->assertInstanceOf(Fruit::class, $result);
        $this->assertGreaterThan(0, $result->getId()); // Should use time() as fallback
        $this->assertEquals('Apple', $result->getName());
        $this->assertEquals(150.0, $result->getQuantityInGrams());
    }
} 