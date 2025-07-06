<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FruitControllerTest extends WebTestCase
{
    private const API_FRUITS_PATH = '/api/fruits';

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetFruits(): void
    {
        $fruitData = [
            'id' => 1,
            'name' => 'Apple',
            'type' => 'fruit',
            'quantity' => 1000,
            'unit' => 'g'
        ];

        $this->client->request('POST', self::API_FRUITS_PATH, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($fruitData));

        $this->client->request('GET', self::API_FRUITS_PATH);

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        
        $this->assertArrayHasKey('fruits', $responseData['data']);
        $this->assertArrayNotHasKey('vegetables', $responseData['data']);
        
        $fruitNames = array_column($responseData['data']['fruits'], 'name');
        $this->assertContains('Apple', $fruitNames);
    }

    public function testGetFruitsWithFilters(): void
    {
        $fruitData = [
            'id' => 1,
            'name' => 'Apple',
            'type' => 'fruit',
            'quantity' => 1000,
            'unit' => 'g'
        ];

        $this->client->request('POST', self::API_FRUITS_PATH, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($fruitData));

        $this->client->request('GET', self::API_FRUITS_PATH . '?unit=g');

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        
        $this->assertArrayHasKey('fruits', $responseData['data']);
        
        foreach ($responseData['data']['fruits'] as $fruit) {
            $this->assertEquals('g', $fruit['unit']);
        }
    }

    public function testGetFruitsWithQuantityFilters(): void
    {
        $fruitData = [
            'id' => 1,
            'name' => 'Apple',
            'type' => 'fruit',
            'quantity' => 1000,
            'unit' => 'g'
        ];

        $this->client->request('POST', self::API_FRUITS_PATH, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($fruitData));

        $this->client->request('GET', self::API_FRUITS_PATH . '?min_quantity=500&max_quantity=1500');

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        
        $this->assertArrayHasKey('fruits', $responseData['data']);
        
        foreach ($responseData['data']['fruits'] as $fruit) {
            $this->assertGreaterThanOrEqual(500, $fruit['quantity']);
            $this->assertLessThanOrEqual(1500, $fruit['quantity']);
        }
    }

    public function testAddFruit(): void
    {
        $fruitData = [
            'id' => 1,
            'name' => 'Banana',
            'type' => 'fruit',
            'quantity' => 2000,
            'unit' => 'g'
        ];

        $this->client->request('POST', self::API_FRUITS_PATH, [], [], ['CONTENT_TYPE' => 'application/json',], json_encode($fruitData));

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        
        $this->assertArrayHasKey('fruits', $responseData['data']);
        
        $fruitNames = array_column($responseData['data']['fruits'], 'name');
        $this->assertContains('Banana', $fruitNames);
    }

    public function testAddFruitWithInvalidData(): void
    {
        $invalidData = [
            'id' => 1,
            'name' => '', // Invalid: empty name
            'quantity' => -5, // Invalid: negative quantity
            'unit' => 'invalid_unit', // Invalid: wrong unit
            'type' => 'invalid_type' // Invalid: wrong type
        ];

        $this->client->request('POST', self::API_FRUITS_PATH, [], [], ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'], json_encode($invalidData));

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

    public function testDeleteFruit(): void
    {
        $fruitData = [
            'id' => 1,
            'name' => 'Orange',
            'type' => 'fruit',
            'quantity' => 1500,
            'unit' => 'g'
        ];

        $this->client->request('POST', self::API_FRUITS_PATH, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($fruitData));

        $this->client->request('GET', self::API_FRUITS_PATH);
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $fruitNames = array_column($responseData['data']['fruits'], 'name');
        $this->assertContains('Orange', $fruitNames);

        $this->client->request('DELETE', self::API_FRUITS_PATH . '/1');

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        
        $this->client->request('GET', self::API_FRUITS_PATH);
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        $fruitNames = array_column($responseData['data']['fruits'], 'name');
        $this->assertNotContains('Orange', $fruitNames);
    }

    public function testDeleteFruitNotFound(): void
    {
        $this->client->request('DELETE', self::API_FRUITS_PATH . '/999');

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testGetFruitsWithInvalidFilters(): void
    {
        $this->client->request('GET', self::API_FRUITS_PATH . '?type=invalid_type&unit=invalid_unit');

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
} 