# Souvenir Recommendation System

A Laravel 11 backend system for providing souvenir recommendations using a Random Forest model via external API integration.

## Architecture Overview

This system follows SOLID principles with the following components:

### Database Schema

#### Souvenirs Table
- `id` - Primary key
- `name` - Souvenir name
- `category` - Category classification
- `price_range` - Enum: low, medium, high
- `description` - Optional text description
- `image_path` - Optional image file path
- `created_at` / `updated_at` - Timestamps

#### Recommendations Table
- `id` - Primary key
- `age` - User's age (integer)
- `status` - Enum: student, worker
- `budget` - User's budget (decimal)
- `purpose` - Enum: family, colleague, partner
- `predicted_category` - ML model prediction result
- `created_at` / `updated_at` - Timestamps

### Service Layer

#### RecommendationServiceInterface
Defines the contract for recommendation services, enabling dependency inversion.

#### RecommendationService
Implements the interface using Laravel's Http facade to communicate with the external Random Forest API (n8n/FastAPI).

#### RecommendationApiException
Custom exception for API-related failures with proper HTTP response handling.

### Controller Layer

#### SouvenirController
- Validates incoming request data
- Calls the recommendation service
- Logs recommendation requests
- Queries and returns matching souvenirs

## Installation

1. **Setup Environment Variables:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

2. **Configure Recommendation API:**
   Update `.env` file with your Random Forest API endpoint:
   ```
   RECOMMENDATION_API_URL=http://localhost:8000/predict
   RECOMMENDATION_API_TIMEOUT=30
   ```

3. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

4. **Start Development Server:**
   ```bash
   php artisan serve
   ```

## API Usage

### Get Recommendations

**Endpoint:** `POST /api/recommend`

**Request Body:**
```json
{
    "age": 25,
    "status": "student",
    "budget": 50.00,
    "purpose": "family"
}
```

**Validation Rules:**
- `age`: Required, integer, 1-120
- `status`: Required, must be 'student' or 'worker'
- `budget`: Required, numeric, minimum 0
- `purpose`: Required, must be 'family', 'colleague', or 'partner'

**Success Response (200):**
```json
{
    "success": true,
    "predicted_category": "handicrafts",
    "recommendations": [
        {
            "id": 1,
            "name": "Traditional Wooden Carving",
            "category": "handicrafts",
            "price_range": "medium",
            "description": "Hand-carved wooden item",
            "image_path": null
        }
    ],
    "total": 1
}
```

**Error Response (503 - API Unavailable):**
```json
{
    "success": false,
    "error": "Unable to process recommendation request",
    "message": "API request failed with status code: 500"
}
```

## External API Integration

The system expects the external Random Forest API to return responses in the following format:

```json
{
    "predicted_category": "handicrafts"
}
```

## SOLID Principles Applied

### Single Responsibility Principle
- **RecommendationService**: Handles API communication only
- **SouvenirController**: Manages HTTP requests and responses
- **Models**: Handle database interactions

### Open/Closed Principle
- New recommendation algorithms can be added without modifying existing code
- Extensible through interface-based design

### Liskov Substitution Principle
- Any implementation of RecommendationServiceInterface can be used interchangeably
- Enables easy testing with mock implementations

### Interface Segregation Principle
- RecommendationServiceInterface provides only necessary methods
- Small, focused contract

### Dependency Inversion Principle
- Controller depends on abstraction (RecommendationServiceInterface)
- Service container handles concrete implementation injection

## Testing

The architecture supports easy testing through dependency injection:

```php
// Test with mock service
$mockService = Mockery::mock(RecommendationServiceInterface::class);
$mockService->shouldReceive('predictCategory')
    ->andReturn('handicrafts');

$this->app->instance(RecommendationServiceInterface::class, $mockService);
```

## File Structure

```
app/
├── Services/
│   ├── Contracts/
│   │   └── RecommendationServiceInterface.php
│   └── RecommendationService.php
├── Exceptions/
│   └── RecommendationApiException.php
├── Http/Controllers/
│   └── SouvenirController.php
├── Models/
│   ├── Souvenir.php
│   └── Recommendation.php
└── Providers/
    └── RecommendationServiceProvider.php

database/migrations/
├── xxxx_xx_xx_create_souvenirs_table.php
└── xxxx_xx_xx_create_recommendations_table.php

routes/
└── api.php

config/
└── services.php (updated)
```

## Future Enhancements

- Caching layer for API responses
- Additional recommendation algorithms
- User authentication and personalization
- Analytics dashboard for recommendation performance
- A/B testing framework for different models
