# E-Procurement API Documentation

Welcome to the E-Procurement System API. This is a detailed guide for interacting with the API.

## Getting Started

### Base URL

All API URLs referenced in this documentation are prefixed with `/api/v1`. The full base URL is:

```
https://base_url/api/v1
```

### Authentication

Most endpoints in this API are protected and require a Bearer Token for access. To authenticate, you must first register a user and then log in. The login and register endpoints will return a token.

You must include this token in the Authorization header for all subsequent protected requests.

**Example Header:**

```
Authorization: Bearer <your_token>
```

## API Endpoints

### 1. Authentication

#### POST `/register`

Registers a new user account and returns the user data along with an API token.

**Request Body:**

| Field                 | Type   | Description                          | Required |
|-----------------------|--------|--------------------------------------|----------|
| name                  | String | The user's full name                 | Yes      |
| email                 | String | The user's email address (unique)    | Yes      |
| password              | String | The user's password (min 8 chars)    | Yes      |
| password_confirmation | String | Must match the password field        | Yes      |

**Success Response (201 Created):**

```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "uuid": "9c15b1a0-a1b2-4c3d-8e4f-5g6h7i8j9k0l",
      "name": "John Doe",
      "email": "john.doe@example.com",
      "created_at": "2025-07-12 15:30:00"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz123456"
  }
}
```

**Error Response (422 Unprocessable Entity):**

```json
{
  "message": "The email has already been taken.",
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
```

#### POST `/login`

Authenticates a user and returns their data along with a new API token.

**Request Body:**

| Field   | Type   | Description             | Required |
|---------|--------|-------------------------|----------|
| email   | String | The user's email address| Yes      |
| password| String | The user's password     | Yes      |

**Success Response (200 OK):**

```json
{
  "success": true,
  "message": "User logged in successfully",
  "data": {
    "user": {
      "uuid": "9c15b1a0-a1b2-4c3d-8e4f-5g6h7i8j9k0l",
      "name": "John Doe",
      "email": "john.doe@example.com",
      "created_at": "2025-07-12 15:30:00"
    },
    "token": "2|abcdefghijklmnopqrstuvwxyz789012"
  }
}
```

**Error Response (422 Unprocessable Entity):**

```json
{
  "message": "The provided credentials do not match our records.",
  "errors": {
    "email": [
      "The provided credentials do not match our records."
    ]
  }
}
```

#### POST `/logout`

Logs the user out by revoking the current API token.

**Authentication:** Required

**Success Response (200 OK):**

```json
{
  "success": true,
  "message": "User logged out successfully"
}
```

---

### 2. Vendor Management

#### POST `/vendor`

Creates a vendor profile and associates it with the authenticated user.

**Authentication:** Required

**Request Body:**

| Field        | Type   | Description                   | Required |
|--------------|--------|-------------------------------|----------|
| name         | String | The legal name of the company | Yes      |
| address      | String | The physical address          | Yes      |
| phone_number | String | Contact phone number          | Yes      |

**Success Response (201 Created):**

```json
{
  "success": true,
  "message": "Vendor registered successfully",
  "data": {
    "uuid": "8b14a2b1-b2c3-5d4e-9f5g-6h7i8j9k0l1m",
    "name": "Global Tech Inc.",
    "address": "123 Tech Street, Silicon Valley",
    "phone_number": "1234567890",
    "user": null
  }
}
```

**Error Response (422 Unprocessable Entity):**

User already has a vendor profile.

---

### 3. Product Catalog

#### GET `/products`

Retrieves a paginated list of all products for the authenticated vendor.

**Authentication:** Required

**Query Parameters:**

| Parameter | Type    | Description            | Default |
|-----------|---------|------------------------|---------|
| page      | Integer | Page number            | 1       |
| perPage   | Integer | Items per page         | 10      |

**Success Response (200 OK):**

```json
{
  "success": true,
  "message": "Products retrieved successfully.",
  "data": [
    {
      "uuid": "prod-uuid-123",
      "name": "Laptop Pro",
      "details": "A high-performance laptop.",
      "price": "1500.00",
      "stock": 50,
      "vendor_uuid": "vendor-uuid-456"
    }
  ],
  "links": {
    "first": "http://localhost/api/v1/products?page=1",
    "last": "http://localhost/api/v1/products?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://localhost/api/v1/products",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

#### POST `/products`

Creates a new product for the authenticated user's vendor.

**Authentication:** Required

**Request Body:**

| Field      | Type    | Description            | Required |
|------------|---------|------------------------|----------|
| name       | String  | Product name           | Yes      |
| description| String  | Detailed description   | Yes      |
| price      | Numeric | Product price          | Yes      |
| stock      | Integer | Available quantity     | Yes      |

**Success Response (201 Created):**

```json
{
  "success": true,
  "message": "Product created successfully.",
  "data": {
    "uuid": "prod-uuid-789",
    "name": "Wireless Mouse",
    "details": "An ergonomic wireless mouse.",
    "price": "75.50",
    "stock": 200,
    "vendor_uuid": "vendor-uuid-456"
  }
}
```

#### GET `/products/{product_uuid}`

Retrieves a single product by UUID.

**Authentication:** Required

**URL Parameter:**

| Parameter     | Type   | Description             |
|---------------|--------|-------------------------|
| product_uuid  | String | UUID of the product     |

**Success Response (200 OK):**

```json
{
  "success": true,
  "message": "Product retrieved successfully.",
  "data": {
    "uuid": "prod-uuid-123",
    "name": "Laptop Pro",
    "details": "A high-performance laptop.",
    "price": "1500.00",
    "stock": 50,
    "vendor_uuid": "vendor-uuid-456"
  }
}
```

**Error Response (404 Not Found):**

Product not found or does not belong to the user.

#### PUT `/products/{product_uuid}`

Updates an existing product by UUID.

**Authentication:** Required

**URL Parameter:**

| Parameter     | Type   | Description             |
|---------------|--------|-------------------------|
| product_uuid  | String | UUID of the product     |

**Request Body:** Same fields as POST `/products`. All optional.

**Success Response (200 OK):**

```json
{
  "success": true,
  "message": "Product updated successfully.",
  "data": {
    "uuid": "prod-uuid-123",
    "name": "Laptop Pro X",
    "details": "An updated high-performance laptop.",
    "price": "1550.00",
    "stock": 45,
    "vendor_uuid": "vendor-uuid-456"
  }
}
```

#### DELETE `/products/{product_uuid}`

Deletes a product by UUID.

**Authentication:** Required

**URL Parameter:**

| Parameter     | Type   | Description             |
|---------------|--------|-------------------------|
| product_uuid  | String | UUID of the product     |

**Success Response (200 OK):**

```json
{
  "success": true,
  "message": "Product deleted successfully.",
  "data": null
}
```
