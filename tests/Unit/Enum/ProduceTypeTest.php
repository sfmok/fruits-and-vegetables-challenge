<?php

declare(strict_types=1);

namespace App\Tests\Unit\Enum;

use App\Enum\ProduceType;
use PHPUnit\Framework\TestCase;

class ProduceTypeTest extends TestCase
{
    public function testFruitValue(): void
    {
        $this->assertEquals('fruit', ProduceType::Fruit->value);
    }

    public function testVegetableValue(): void
    {
        $this->assertEquals('vegetable', ProduceType::Vegetable->value);
    }

    public function testGetPluralForFruit(): void
    {
        $this->assertEquals('fruits', ProduceType::Fruit->getPlural());
    }

    public function testGetPluralForVegetable(): void
    {
        $this->assertEquals('vegetables', ProduceType::Vegetable->getPlural());
    }

    public function testEnumCases(): void
    {
        $cases = ProduceType::cases();
        
        $this->assertCount(2, $cases);
        $this->assertContains(ProduceType::Fruit, $cases);
        $this->assertContains(ProduceType::Vegetable, $cases);
    }

    public function testFromString(): void
    {
        $fruit = ProduceType::from('fruit');
        $this->assertEquals(ProduceType::Fruit, $fruit);

        $vegetable = ProduceType::from('vegetable');
        $this->assertEquals(ProduceType::Vegetable, $vegetable);
    }

    public function testTryFrom(): void
    {
        $fruit = ProduceType::tryFrom('fruit');
        $this->assertEquals(ProduceType::Fruit, $fruit);

        $vegetable = ProduceType::tryFrom('vegetable');
        $this->assertEquals(ProduceType::Vegetable, $vegetable);

        $invalid = ProduceType::tryFrom('invalid');
        $this->assertNull($invalid);
    }
} 