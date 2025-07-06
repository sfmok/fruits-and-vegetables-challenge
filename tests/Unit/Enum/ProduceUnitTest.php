<?php

declare(strict_types=1);

namespace App\Tests\Unit\Enum;

use App\Enum\ProduceUnit;
use PHPUnit\Framework\TestCase;

class ProduceUnitTest extends TestCase
{
    public function testKilogramValue(): void
    {
        $this->assertEquals('kg', ProduceUnit::Kilogram->value);
    }

    public function testGramValue(): void
    {
        $this->assertEquals('g', ProduceUnit::Gram->value);
    }

    public function testEnumCases(): void
    {
        $cases = ProduceUnit::cases();
        
        $this->assertCount(2, $cases);
        $this->assertContains(ProduceUnit::Kilogram, $cases);
        $this->assertContains(ProduceUnit::Gram, $cases);
    }

    public function testFromString(): void
    {
        $kilogram = ProduceUnit::from('kg');
        $this->assertEquals(ProduceUnit::Kilogram, $kilogram);

        $gram = ProduceUnit::from('g');
        $this->assertEquals(ProduceUnit::Gram, $gram);
    }

    public function testTryFrom(): void
    {
        $kilogram = ProduceUnit::tryFrom('kg');
        $this->assertEquals(ProduceUnit::Kilogram, $kilogram);

        $gram = ProduceUnit::tryFrom('g');
        $this->assertEquals(ProduceUnit::Gram, $gram);

        $invalid = ProduceUnit::tryFrom('invalid');
        $this->assertNull($invalid);
    }

    public function testConvertToGramsFromKilograms(): void
    {
        $result = ProduceUnit::Kilogram->convertToGrams(1.5);
        $this->assertEquals(1500.0, $result);
    }

    public function testConvertFromGramsToKilograms(): void
    {
        $result = ProduceUnit::Kilogram->convertFromGrams(1500.0);
        $this->assertEquals(1.5, $result);
    }
} 