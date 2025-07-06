# 🍎🥕 Fruits and Vegetables Challenge - Implementation

## 🎯 Challenge Overview
This project implements a service that processes a `request.json` file containing fruits and vegetables data, creating separate collections with full CRUD operations, unit conversion capabilities, and RESTful API endpoints.

## 🏗️ Architecture & Design Approach

### Core Design Principles
- **SOLID Principles**: Clean separation of concerns with interfaces and abstractions
- **Domain-Driven Design**: Rich domain models with encapsulated business logic
- **Repository Pattern**: Abstract storage layer for data persistence
- **Factory Pattern**: Dynamic object creation based on produce types
- **Strategy Pattern**: Different collection implementations for different produce types

### Key Architectural Decisions

#### 1. **Abstract Base Class Pattern**
- `Produce` abstract class provides common functionality for both `Fruit` and `Vegetable` entities
- All quantities are internally stored in grams for consistency
- Unit conversion logic is centralized in the `ProduceUnit` enum

#### 2. **Collection Management**
- `CollectionInterface` defines the contract for all collections
- `AbstractCollection` provides shared filtering logic
- `CollectionResolver` uses dependency injection to resolve appropriate collections
- Separate `FruitCollection` and `VegetableCollection` implementations

#### 3. **Storage Strategy**
- `FileStorage` implementation using JSON files for persistence
- Storage is abstracted through `StorageInterface`
- Data is automatically serialized/deserialized with proper type handling

#### 4. **Service Layer**
- `CollectionService` orchestrates all business operations
- Clean separation between domain logic and infrastructure concerns
- Exception handling for business rule violations

## 📁 Project Structure

```
src/
├── Collection/           # Collection management
│   ├── AbstractCollection.php
│   ├── CollectionInterface.php
│   ├── CollectionResolver.php
│   ├── FruitCollection.php
│   └── VegetableCollection.php
├── Controller/          # REST API endpoints
│   ├── FruitController.php
│   ├── ProduceController.php
│   └── VegetableController.php
├── Dto/                # Data Transfer Objects
│   ├── ProduceFiltersInput.php
│   └── ProduceInput.php
├── Entity/             # Domain entities
│   ├── Fruit.php
│   ├── Produce.php
│   └── Vegetable.php
├── Enum/               # Enumerations
│   ├── ProduceType.php
│   └── ProduceUnit.php
├── Exception/          # Custom exceptions
│   └── ProduceNotFoundException.php
├── Factory/            # Object factories
│   └── ProduceFactory.php
├── Service/            # Business logic services
│   └── CollectionService.php
└── Storage/            # Data persistence
    ├── FileStorage.php
    └── StorageInterface.php
```

## 🚀 Setup Instructions

### Prerequisites
- PHP 8.4
- Composer

### Installation

1. **Clone the repository**
   ```bash
   git clone git@github.com:sfmok/fruits-and-vegetables-challenge.git
   cd fruits-and-vegetables-challenge
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

## 🐳 Docker Setup (Recommended)

### Using Docker Compose

1. **Clone the repository**
   ```bash
   git clone git@github.com:sfmok/fruits-and-vegetables-challenge.git
   cd fruits-and-vegetables-challenge
   ```

2. **Start the application**
   ```bash
   docker compose up -d
   ```
   This will automatically:
   - Build the Docker image
   - Start the PHP server on port 8080
   - Mount your code for development

3. **Install dependencies inside container**
   ```bash
   docker exec -it php composer install
   ```

- The PHP server runs automatically because it's configured in the `compose.yaml` file with the command: `php -S 0.0.0.0:8080 -t /app/public`
- If you go with docker setup, ensure you access to container first:
```bash
docker exec -it php sh
```

## 🧪 Testing

### Running PHPUnit Tests
```bash
# Run all tests
bin/phpunit
```

### Running Psalm Static Analysis
```bash
# Run Psalm analysis
vendor/bin/psalm
```

## 🌐 API Usage

### Complete API Endpoints

#### 1. **General Produces Endpoints** (`/api/produces`)
```http
# Add multiple produces (fruits and vegetables)
POST /api/produces

# Get all produces with optional filters
GET /api/produces
GET /api/produces?type=fruit
GET /api/produces?type=vegetable
GET /api/produces?unit=kg
GET /api/produces?unit=g
GET /api/produces?name=Apple
GET /api/produces?min_quantity=1000&max_quantity=5000
```

#### 2. **Fruits Endpoints** (`/api/fruits`)
```http
# Get all fruits with optional filters
GET /api/fruits
GET /api/fruits?unit=kg
GET /api/fruits?name=Apple
GET /api/fruits?min_quantity=1000&max_quantity=5000

# Add a single fruit
POST /api/fruits

# Delete a fruit by ID
DELETE /api/fruits/{id}
```

#### 3. **Vegetables Endpoints** (`/api/vegetables`)
```http
# Get all vegetables with optional filters
GET /api/vegetables
GET /api/vegetables?unit=kg
GET /api/vegetables?name=Carrot
GET /api/vegetables?min_quantity=1000&max_quantity=5000

# Add a single vegetable
POST /api/vegetables

# Delete a vegetable by ID
DELETE /api/vegetables/{id}
```

### Using `request.json` with cURL

#### Load the sample data using `request.json`
```bash
# Add all produces from request.json
curl -X POST http://localhost:8080/api/produces \
  -H "Content-Type: application/json" \
  -d @request.json
```

#### Individual produce examples
```bash
# Add a single fruit
curl -X POST http://localhost:8080/api/fruits \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1,
    "name": "Apple",
    "type": "fruit",
    "quantity": 500,
    "unit": "g"
  }'

# Add a single vegetable
curl -X POST http://localhost:8080/api/vegetables \
  -H "Content-Type: application/json" \
  -d '{
    "id": 2,
    "name": "Carrot",
    "type": "vegetable",
    "quantity": 1.5,
    "unit": "kg"
  }'

# Get all fruits
curl -X GET http://localhost:8080/api/fruits

# Get all vegetables in kilograms
curl -X GET "http://localhost:8080/api/vegetables?unit=kg"

# Get produces filtered by name
curl -X GET "http://localhost:8080/api/produces?name=Apple"

# Get produces with quantity range (in grams)
curl -X GET "http://localhost:8080/api/produces?min_quantity=1000&max_quantity=10000"

# Delete a fruit by ID
curl -X DELETE http://localhost:8080/api/fruits/1

# Delete a vegetable by ID
curl -X DELETE http://localhost:8080/api/vegetables/2
```

### Example API Responses

#### Successful Response (GET /api/produces)
```json
{
  "data": {
    "fruits": [
      {
        "id": 1,
        "name": "Apple",
        "quantity": 500,
        "unit": "g"
      }
    ],
    "vegetables": [
      {
        "id": 2,
        "name": "Carrot",
        "quantity": 1.5,
        "unit": "kg"
      }
    ]
  }
}
```

#### Successful Response (POST /api/fruits)
```json
{
  "data": {
    "fruits": [
      {
        "id": 1,
        "name": "Apple",
        "quantity": 500,
        "unit": "g"
      }
    ]
  }
}
```

#### Error Response (Validation Error)
```json
{
  "violations": [
    {
      "title": "Name is required",
      ...
    },
    {
      "title": "Quantity must be positive",
      ...
    }
  ]
}
```

## 🔧 Key Features Implemented

### ✅ Core Requirements
- ✅ Process `request.json` and create separate collections
- ✅ Collection methods: `add()`, `remove()`, `list()`
- ✅ All quantities stored in grams internally
- ✅ File-based storage engine (JSON files)
- ✅ REST API endpoints for querying and adding items
- ✅ Filtering capabilities on API endpoints

### ✅ Bonus Features
- ✅ Unit conversion (grams ↔ kilograms)
- ✅ Search functionality in collections
- ✅ Latest Symfony 7.3 framework
- ✅ Comprehensive input validation
- ✅ Exception handling
- ✅ Full test coverage (Unit + Integration)

## 🧪 Testing Strategy

### Test Coverage
- **Unit Tests**: Individual components and business logic
- **Integration Tests**: API endpoints and full request/response cycles
- **Static Analysis**: Psalm for type safety and code quality

### Test Structure
```
tests/
├── Integration/
│   └── Controller/     # API endpoint tests
└── Unit/
    ├── Collection/     # Collection logic tests
    ├── Dto/           # Data validation tests
    ├── Entity/        # Domain model tests
    ├── Enum/          # Enumeration tests
    ├── Factory/       # Factory pattern tests
    ├── Service/       # Business logic tests
    └── Storage/       # Data persistence tests
```

## 🔍 Code Quality

### Static Analysis
- **Psalm**: Type safety and error detection
- **PHPUnit**: Unit and integration testing
- **Symfony Validator**: Input validation and constraints

---

## 🎯 Original Challenge Description

### Goal
We want to build a service which will take a `request.json` and:
* Process the file and create two separate collections for `Fruits` and `Vegetables`
* Each collection has methods like `add()`, `remove()`, `list()`;
* Units have to be stored as grams;
* Store the collections in a storage engine of your choice. (e.g. Database, In-memory)
* Provide an API endpoint to query the collections. As a bonus, this endpoint can accept filters to be applied to the returning collection.
* Provide another API endpoint to add new items to the collections (i.e., your storage engine).
* As a bonus you might:
  * consider giving an option to decide which units are returned (kilograms/grams);
  * how to implement `search()` method collections;
  * use latest version of Symfony's to embed your logic 

### ✔️ How can I check if my code is working?
You have two ways of moving on:
* You call the Service from PHPUnit test like it's done in dummy test (just run `bin/phpunit` from the console)

or

* You create a Controller which will be calling the service with a json payload

## 💡 Hints before you start working on it
* Keep KISS, DRY, YAGNI, SOLID principles in mind
* We value a clean domain model, without unnecessary code duplication or complexity
* Think about how you will handle input validation
* Follow generally-accepted good practices, such as no logic in controllers, information hiding (see the first hint).
* Timebox your work - we expect that you would spend between 3 and 4 hours.
* Your code should be tested
* We don't care how you handle data persistence, no bonus points for having a complex method

## When you are finished
* Please upload your code to a public git repository (i.e. GitHub, Gitlab)
