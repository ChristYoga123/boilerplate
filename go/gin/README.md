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

## API Documentation

Full OpenAPI 3.0 (Swagger) spec: [`documentation/api.yaml`](documentation/api.yaml)

You can import it directly into [Swagger Editor](https://editor.swagger.io) or Postman.

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
- Issues a new token and invalidates the old one.
- `200`: new JWT token | `401`: unauthorized

### Users (Protected)

All endpoints below require `Authorization: Bearer <token>`.

`PUT /api/v1/users/me`

- Request body: `username`, `email`, `password`
- `200`: updated user | `400`: validation errors

`DELETE /api/v1/users/me`

- Soft-deletes the current user.
- `200`: deleted user

`POST /api/v1/users/logout`

- Invalidates the current JWT token in Redis.
- `200`: logged out

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
- Soft delete uses unix timestamp (`deleted_at = 0` means active, non-zero means deleted) via `gorm.io/plugin/soft_delete`. This allows composite unique indexes `(email, deleted_at)` and `(username, deleted_at)` to work correctly in MySQL — a previously deleted user can re-register with the same email/username.

