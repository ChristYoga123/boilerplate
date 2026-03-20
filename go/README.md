# Go Project (Gin + GORM + JWT + Redis)

A small Go REST API boilerplate built with:

- `gin-gonic/gin` for HTTP routing
- `gorm` for database access (SQLite/MySQL/PostgreSQL)
- `bcrypt` for password hashing
- JWT for authentication
- Redis-backed token invalidation (logout / token rotation)

## Features

- Register and login with hashed passwords
- JWT authentication for protected user routes
- Logout by invalidating the current token in Redis
- Standardized response format:
  - `SuccessResponse`: `{ "success": true, "message": "...", "data": ... }`
  - `ErrorResponse`: `{ "success": false, "message": "...", "errors": { ... } }` (optional)

## Base URL

- All endpoints are under: `/api/v1`

## API Endpoints

### Auth

`POST /api/v1/auth/register`

- Request body (`RegisterRequest`):
  - `username` (required, string)
  - `email` (required, valid email)
  - `password` (required, string)
- Responses:
  - `201`: user created (`RegisterResponse`)
  - `409`: duplicate username/email (when detected)
  - `400`: validation errors

`POST /api/v1/auth/login`

- Request body (`LoginRequest`):
  - `email` (required, valid email)
  - `password` (required, string)
- Responses:
  - `200`: returns JWT token (`LoginResponse`)
  - `401`: invalid credentials

### Users (Protected)

All endpoints below require an `Authorization` header:

- `Authorization: Bearer <token>`

`PUT /api/v1/users/me`

- Middleware stores `user_id` in Gin context after validating the JWT and checking token activity in Redis.
- Request body (`UpdateUserRequest`):
  - `username` (required)
  - `email` (required, valid email)
  - `password` (required)
- Response:
  - `200`: updated user (`UpdateUserResponse`)

`DELETE /api/v1/users/me`

- Response:
  - `200`: deleted user (`DeleteUserResponse`)

`POST /api/v1/users/logout`

- Invalidates the current JWT token by deleting the Redis key for that user.
- Response:
  - `200`: logout success

## Request Validation & Error Format

- Validation errors from Gin’s binding/validator are translated into an `errors` map via `helpers.TranslateError`.
- Duplicate detection is best-effort based on database error messages (e.g. unique constraint violations).

## Configuration

Configuration is loaded from environment variables using `godotenv.Load()`.
Because the app loads `.env` at runtime, create `.env` inside `go/` (or export env vars before running).

Key variables:

- `APP_PORT` (default: `3000`)
- `DB_DRIVER` (default: `sqlite`)
- `DB_DSN` (default: `database.db`)
  - For SQLite, the default DSN creates/uses `database.db`.
  - For MySQL/PostgreSQL, set `DB_DSN` to a proper GORM DSN for your database.
- `JWT_SECRET` (used to sign JWTs)
- `JWT_EXPIRY_HOURS` (default: `24`)
- `REDIS_ADDR` (default: `localhost:6379`)
- `REDIS_PASSWORD` (default: empty)
- `REDIS_DB` (default: `0`)

An example file is available at `go/.env.example`.

## Running the Server

From the `go/` directory:

1. Ensure environment variables are set (create `go/.env` based on `go/.env.example`)
2. Run:
   - `go mod download`
   - `go run ./cmd`

The server listens on `:${APP_PORT}`.

## Project Structure

- `go/cmd/main.go`: application bootstrap
- `go/internal/routes/routes.go`: route registration
- `go/internal/controllers/*`: HTTP handlers (Gin)
- `go/internal/services/*`: business logic (user + JWT)
- `go/internal/repositories/*`: database queries (GORM)
- `go/internal/middlewares/*`: auth middleware (JWT + Redis token activity)
- `go/internal/configs/*`: config + DB/Redis setup
- `go/internal/dtos/*`: request/response models

## Notes

- This project invalidates tokens by deleting the Redis value for the user. If you issue a new token, the previous token becomes inactive.

