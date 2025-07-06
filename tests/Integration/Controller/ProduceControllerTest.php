<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProduceControllerTest extends WebTestCase
{
    private const API_PRODUCES_PATH = '/api/produces';
    private const JSON_REQUEST_PATH = __DIR__ . '/../../../request.json';

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testAddProduces(): void
    {
        $json = file_get_contents(self::JSON_REQUEST_PATH);
        
        $this->client->request('POST', self::API_PRODUCES_PATH, [], [], ['CONTENT_TYPE' => 'application/json'], $json);

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        
        $this->assertArrayHasKey('fruits', $responseData['data']);
        $this->assertArrayHasKey('vegetables', $responseData['data']);
        
        $allItems = array_merge($responseData['data']['fruits'], $responseData['data']['vegetables']);
        $itemNames = array_column($allItems, 'name');
        $this->assertContains('Carrot', $itemNames);
        $this->assertContains('Apples', $itemNames);
        $this->assertContains('Pears', $itemNames);
    }

    public function testGetProducesWithoutFilters(): void
    {
        $json = file_get_contents(self::JSON_REQUEST_PATH);
        
        $this->client->request('POST', self::API_PRODUCES_PATH, [], [], ['CONTENT_TYPE' => 'application/json'], $json);

        $this->client->request('GET', self::API_PRODUCES_PATH);

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        
        $this->assertArrayHasKey('fruits', $responseData['data']);
        $this->assertArrayHasKey('vegetables', $responseData['data']);
    }

    public function testGetProducesWithTypeFilter(): void
    {
        $json = file_get_contents(self::JSON_REQUEST_PATH);

        $this->client->request('POST', self::API_PRODUCES_PATH, [], [], ['CONTENT_TYPE' => 'application/json'], $json);

        $this->client->request('GET', self::API_PRODUCES_PATH . '?type=fruit');

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        
        $this->assertArrayHasKey('fruits', $responseData['data']);
        $this->assertArrayNotHasKey('vegetables', $responseData['data']);
        
        $fruitNames = array_column($responseData['data']['fruits'], 'name');
        $this->assertContains('Apples', $fruitNames);
        $this->assertContains('Pears', $fruitNames);
        $this->assertContains('Melons', $fruitNames);
    }

    public function testGetProducesWithQuantityFilters(): void
    {
        $json = file_get_contents(self::JSON_REQUEST_PATH);

        $this->client->request('POST', self::API_PRODUCES_PATH, [], [], ['CONTENT_TYPE' => 'application/json'], $json);

        $this->client->request('GET', self::API_PRODUCES_PATH . '?min_quantity=1000&max_quantity=10000');

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        
        $this->assertArrayHasKey('fruits', $responseData['data']);
        $this->assertArrayHasKey('vegetables', $responseData['data']);
        
        $allItems = array_merge($responseData['data']['fruits'], $responseData['data']['vegetables']);
        $this->assertNotEmpty($allItems);
        foreach ($allItems as $item) {
            $this->assertGreaterThanOrEqual(1000, $item['quantity']);
            $this->assertLessThanOrEqual(10000, $item['quantity']);
        }
    }

    public function testGetProducesWithUnitFilter(): void
    {
        $json = file_get_contents(self::JSON_REQUEST_PATH);

        $this->client->request('POST', self::API_PRODUCES_PATH, [], [], ['CONTENT_TYPE' => 'application/json'], $json);

        $this->client->request('GET', self::API_PRODUCES_PATH . '?unit=kg');

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        
        $this->assertArrayHasKey('fruits', $responseData['data']);
        $this->assertArrayHasKey('vegetables', $responseData['data']);
        
        $allItems = array_merge($responseData['data']['fruits'], $responseData['data']['vegetables']);
        $this->assertNotEmpty($allItems);
        foreach ($allItems as $item) {
            $this->assertEquals('kg', $item['unit']);
        }
    }

    public function testGetProducesWithNameFilter(): void
    {
        $json = file_get_contents(self::JSON_REQUEST_PATH);

        $this->client->request('POST', self::API_PRODUCES_PATH, [], [], ['CONTENT_TYPE' => 'application/json'], $json);

        $this->client->request('GET', self::API_PRODUCES_PATH . '?name=Apple');

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertIsArray($responseData['data']);
        
        $this->assertArrayHasKey('fruits', $responseData['data']);
        $this->assertArrayHasKey('vegetables', $responseData['data']);
        
        $allItems = array_merge($responseData['data']['fruits'], $responseData['data']['vegetables']);
        foreach ($allItems as $item) {
            $this->assertStringContainsString('Apple', $item['name']);
        }
    }

    public function testAddProducesWithInvalidData(): void
    {
        $invalidData = [
            [
                'id' => 1,
                'name' => '', // Invalid: empty name
                'quantity' => -5, // Invalid: negative quantity
                'unit' => 'invalid_unit', // Invalid: wrong unit
                'type' => 'invalid_type' // Invalid: wrong type
            ]
        ];

        $this->client->request('POST', self::API_PRODUCES_PATH, [], [], ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'], json_encode($invalidData));

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

    public function testGetProducesWithInvalidFilters(): void
    {
        $this->client->request('GET', self::API_PRODUCES_PATH . '?type=invalid_type&unit=invalid_unit');

        $response = $this->client->getResponse();
        
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
} 