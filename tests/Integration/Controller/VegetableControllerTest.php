<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class VegetableControllerTest extends WebTestCase
{
    private const API_VEGETABLES_PATH = '/api/vegetables';

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetVegetables(): void
    {
        $vegetableData = [
            'id' => 1,
            'name' => 'Carrot',
            'type' => 'vegetable',
            'quantity' => 1000,
            'unit' => 'g'
        ];

        $this->client->request('POST', self::API_VEGETABLES_PATH, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($vegetableData));

        $this->client->request('GET', self::API_VEGETABLES_PATH);

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        
        $this->assertArrayHasKey('vegetables', $responseData['data']);
        $this->assertArrayNotHasKey('fruits', $responseData['data']);
        
        $vegetableNames = array_column($responseData['data']['vegetables'], 'name');
        $this->assertContains('Carrot', $vegetableNames);
    }

    public function testGetVegetablesWithFilters(): void
    {
        $vegetableData = [
            'id' => 1,
            'name' => 'Carrot',
            'type' => 'vegetable',
            'quantity' => 1000,
            'unit' => 'g'
        ];

        $this->client->request('POST', self::API_VEGETABLES_PATH, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($vegetableData));

        $this->client->request('GET', self::API_VEGETABLES_PATH . '?unit=g');

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        
        $this->assertArrayHasKey('vegetables', $responseData['data']);
        
        foreach ($responseData['data']['vegetables'] as $vegetable) {
            $this->assertEquals('g', $vegetable['unit']);
        }
    }

    public function testGetVegetablesWithQuantityFilters(): void
    {
        $vegetableData = [
            'id' => 1,
            'name' => 'Carrot',
            'type' => 'vegetable',
            'quantity' => 1000,
            'unit' => 'g'
        ];

        $this->client->request('POST', self::API_VEGETABLES_PATH, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($vegetableData));

        $this->client->request('GET', self::API_VEGETABLES_PATH . '?min_quantity=500&max_quantity=1500');

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        
        $this->assertArrayHasKey('vegetables', $responseData['data']);
        
        foreach ($responseData['data']['vegetables'] as $vegetable) {
            $this->assertGreaterThanOrEqual(500, $vegetable['quantity']);
            $this->assertLessThanOrEqual(1500, $vegetable['quantity']);
        }
    }

    public function testAddVegetable(): void
    {
        $vegetableData = [
            'id' => 1,
            'name' => 'Broccoli',
            'type' => 'vegetable',
            'quantity' => 2000,
            'unit' => 'g'
        ];

        $this->client->request('POST', self::API_VEGETABLES_PATH, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($vegetableData));

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        
        $this->assertArrayHasKey('vegetables', $responseData['data']);
        
        // Verify the vegetable was added
        $vegetableNames = array_column($responseData['data']['vegetables'], 'name');
        $this->assertContains('Broccoli', $vegetableNames);
    }

    public function testAddVegetableWithInvalidData(): void
    {
        $invalidData = [
            'id' => 1,
            'name' => '', // Invalid: empty name
            'quantity' => -5, // Invalid: negative quantity
            'unit' => 'invalid_unit', // Invalid: wrong unit
            'type' => 'invalid_type' // Invalid: wrong type
        ];

        $this->client->request('POST', self::API_VEGETABLES_PATH, [], [], ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'], json_encode($invalidData));

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJson($response->getContent());
        
        $responseData = json_decode($response->getContent(), true);
        $violations = $responseData['violations'];
        $this->assertCount(4, $violations);
        $this->assertEquals('Name is required', $violations[0]['title']);
        $this->assertEquals('Quantity must be positive', $violations[1]['title']);
        $this->assertEquals('Unit must be either "g" or "kg"', $violations[2]['title']);
        $this->assertEquals('Type must be either "fruit" or "vegetable"', $violations[3]['title']);
    }

    public function testDeleteVegetable(): void
    {
        $vegetableData = [
            'id' => 1,
            'name' => 'Tomato',
            'type' => 'vegetable',
            'quantity' => 1500,
            'unit' => 'g'
        ];

        $this->client->request('POST', self::API_VEGETABLES_PATH, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($vegetableData));

        $this->client->request('GET', self::API_VEGETABLES_PATH);
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $vegetableNames = array_column($responseData['data']['vegetables'], 'name');
        $this->assertContains('Tomato', $vegetableNames);

        $this->client->request('DELETE', '/api/vegetables/1');

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        
        $this->client->request('GET', self::API_VEGETABLES_PATH);
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $vegetableNames = array_column($responseData['data']['vegetables'], 'name');
        $this->assertNotContains('Tomato', $vegetableNames);
    }

    public function testDeleteVegetableNotFound(): void
    {
        $this->client->request('DELETE', '/api/vegetables/999');

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testGetVegetablesWithInvalidFilters(): void
    {
        $this->client->request('GET', self::API_VEGETABLES_PATH . '?type=invalid_type&unit=invalid_unit');

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
} 