<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory;

use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Enum\ProduceType;
use App\Factory\ProduceFactory;
use PHPUnit\Framework\TestCase;

class ProduceFactoryTest extends TestCase
{
    private ProduceFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ProduceFactory();
    }

    public function testCreateFruitInstance(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Apple',
            'quantity' => 150.0,
            'unit' => 'g',
        ];

        $result = $this->factory->createInstance(ProduceType::Fruit, $data);

        $this->assertInstanceOf(Fruit::class, $result);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('Apple', $result->getName());
        $this->assertEquals(150.0, $result->getQuantityInGrams());
    }

    public function testCreateVegetableInstance(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Carrot',
            'quantity' => 100.0,
            'unit' => 'g',
        ];

        $result = $this->factory->createInstance(ProduceType::Vegetable, $data);

        $this->assertInstanceOf(Vegetable::class, $result);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('Carrot', $result->getName());
        $this->assertEquals(100.0, $result->getQuantityInGrams());
    }

    public function testCreateFruitInstanceWithKilogramUnit(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Apple',
            'quantity' => 1.5,
            'unit' => 'kg',
        ];

        $result = $this->factory->createInstance(ProduceType::Fruit, $data);

        $this->assertInstanceOf(Fruit::class, $result);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('Apple', $result->getName());
        $this->assertEquals(1500.0, $result->getQuantityInGrams());
    }

    public function testCreateVegetableInstanceWithKilogramUnit(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Carrot',
            'quantity' => 2.0,
            'unit' => 'kg',
        ];

        $result = $this->factory->createInstance(ProduceType::Vegetable, $data);

        $this->assertInstanceOf(Vegetable::class, $result);
        $this->assertEquals(1, $result->getId());
        $this->assertEquals('Carrot', $result->getName());
        $this->assertEquals(2000.0, $result->getQuantityInGrams());
    }
} 