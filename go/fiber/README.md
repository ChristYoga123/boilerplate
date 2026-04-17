# Go Project (Fiber + GORM + JWT + Redis)

A small Go REST API boilerplate built with:

- `gofiber/fiber/v3` for HTTP routing
- `gorm` for database access (SQLite/MySQL/PostgreSQL)
- `bcrypt` for password hashing
- JWT for authentication
- Redis-backed token session tracking (logout / token rotation)

## Features

- Register and login with hashed passwords
- JWT authentication for protected user routes
- Logout by invalidating only the current token session in Redis
- Graceful shutdown with startup config validation
- Standardized response format:
  - `SuccessResponse`: `{ "success": true, "message": "...", "data": ... }`
  - `ErrorResponse`: `{ "success": false, "message": "...", "errors": { ... } }` (optional)

## API Documentation

Full OpenAPI 3.0 (Swagger) spec: [`documentation/api.yaml`](documentation/api.yaml)

## Base URL

- All endpoints are under: `/api/v1`

## API Endpoints

### Auth

`POST /api/v1/auth/register`

- Request body: `username`, `email`, `password`
- `201`: user created | `400`: validation errors | `409`: duplicate username/email

`POST /api/v1/auth/login`

- Request body: `email`, `password`
- `200`: returns JWT token | `401`: invalid credentials

`POST /api/v1/auth/refresh`

- Requires: `Authorization: Bearer <token>`
- Issues a new token and invalidates the old token for the current session only.
- `200`: new JWT token | `401`: unauthorized

### Users (Protected)

All endpoints below require `Authorization: Bearer <token>`.

`PUT /api/v1/users/me`

- Request body: `username`, `email`, `password` (optional; omit to keep current password)
- `200`: updated user | `400`: validation errors

`DELETE /api/v1/users/me`

- Soft-deletes the current user.
- `200`: deleted user

`POST /api/v1/users/logout`

- Invalidates the current JWT session in Redis.
- `200`: logged out

## Configuration

Key variables:

- `APP_PORT` (default: `8080`)
- `DB_DRIVER` (default: `sqlite`)
- `DB_DSN` (default: `database.db`)
- `JWT_SECRET` (required, used to sign JWTs)
- `JWT_EXPIRY_HOURS` (default: `24`)
- `REDIS_ADDR` (default: `localhost:6379`)
- `REDIS_PASSWORD` (default: empty)
- `REDIS_DB` (default: `0`)

## Running the Server

1. Create `.env` from `.env.example`
2. Run `go mod tidy`
3. Run `go run ./cmd`

## Notes

- Active JWTs are tracked per session in Redis. Logging out or refreshing invalidates only the current session token, so other logged-in devices can stay active.
- Soft delete uses unix timestamp (`deleted_at = 0` means active, non-zero means deleted) via `gorm.io/plugin/soft_delete`.
- The server supports graceful shutdown. On `SIGINT` (Ctrl+C) or `SIGTERM`, it stops accepting new requests and waits up to 5 seconds for in-flight requests to complete before exiting.
