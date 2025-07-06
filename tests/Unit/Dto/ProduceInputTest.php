<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto;

use App\Dto\ProduceInput;
use App\Enum\ProduceType;
use App\Enum\ProduceUnit;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class ProduceInputTest extends TestCase
{
    public function testProduceInputWithAllParameters(): void
    {
        $produceInput = new ProduceInput(1, 'Apple', 150.0, 'g', 'fruit');

        $this->assertEquals(1, $produceInput->id);
        $this->assertEquals('Apple', $produceInput->name);
        $this->assertEquals(150.0, $produceInput->quantity);
        $this->assertEquals('g', $produceInput->unit);
        $this->assertEquals('fruit', $produceInput->type);
    }

    public function testToArrayMethod(): void
    {
        $produceInput = new ProduceInput(1, 'Carrot', 300.0, 'g', 'vegetable');

        $expected = [
            'id' => 1,
            'name' => 'Carrot',
            'quantity' => 300.0,
            'unit' => 'g',
            'type' => 'vegetable',
        ];

        $this->assertEquals($expected, $produceInput->toArray());
    }

    public function testValidationWithValidData(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $produceInput = new ProduceInput(1, 'Apple', 150.0, 'g', 'fruit');

        $violations = $validator->validate($produceInput);
        $this->assertCount(0, $violations);
    }

    public function testValidationWithValidDataAndNullId(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $produceInput = new ProduceInput(null, 'Banana', 200.0, 'kg', 'fruit');

        $violations = $validator->validate($produceInput);
        $this->assertCount(0, $violations);
    }

    public function testValidationWithEmptyName(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $produceInput = new ProduceInput(1, '', 150.0, 'g', 'fruit');

        $violations = $validator->validate($produceInput);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $this->assertSame('Name is required', $violations[0]->getMessage());
    }

    public function testValidationWithNegativeQuantity(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $produceInput = new ProduceInput(1, 'Apple', -150.0, 'g', 'fruit');

        $violations = $validator->validate($produceInput);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $this->assertSame('Quantity must be positive', $violations[0]->getMessage());
    }

    public function testValidationWithZeroQuantity(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $produceInput = new ProduceInput(1, 'Apple', 0.0, 'g', 'fruit');

        $violations = $validator->validate($produceInput);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $this->assertSame('Quantity must be positive', $violations[0]->getMessage());
    }

    public function testValidationWithInvalidUnit(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $produceInput = new ProduceInput(1, 'Apple', 150.0, 'invalid_unit', 'fruit');

        $violations = $validator->validate($produceInput);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $this->assertSame('Unit must be either "g" or "kg"', $violations[0]->getMessage());
    }

    public function testValidationWithInvalidType(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $produceInput = new ProduceInput(1, 'Apple', 150.0, 'g', 'invalid_type');

        $violations = $validator->validate($produceInput);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $this->assertSame('Type must be either "fruit" or "vegetable"', $violations[0]->getMessage());
    }

    public function testValidationWithValidEnumValues(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $produceInput = new ProduceInput(
            1, 
            'Carrot', 
            300.0, 
            ProduceUnit::Gram->value, 
            ProduceType::Vegetable->value
        );

        $violations = $validator->validate($produceInput);
        $this->assertCount(0, $violations);
    }
} 