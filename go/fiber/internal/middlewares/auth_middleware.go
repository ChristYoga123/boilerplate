package middlewares

import (
	"net/http"
	"strings"

	"github.com/gofiber/fiber/v3"
	"go-project/internal/dtos/responses"
	"go-project/internal/services"
)

func AuthMiddleware(jwtSvc services.JWTServiceInterface) fiber.Handler {
	return func(c fiber.Ctx) error {
		authHeader := c.Get("Authorization")
		if authHeader == "" {
			return c.Status(http.StatusUnauthorized).JSON(responses.ErrorResponse{
				Success: false,
				Message: "Authorization header required",
			})
		}

		parts := strings.SplitN(authHeader, " ", 2)
		if len(parts) != 2 || parts[0] != "Bearer" {
			return c.Status(http.StatusUnauthorized).JSON(responses.ErrorResponse{
				Success: false,
				Message: "Invalid authorization format",
			})
		}

		tokenStr := parts[1]
		claims, err := jwtSvc.ValidateToken(tokenStr)
		if err != nil {
			return c.Status(http.StatusUnauthorized).JSON(responses.ErrorResponse{
				Success: false,
				Message: "Invalid token",
			})
		}
		if !jwtSvc.IsTokenActive(c.Context(), claims.UserID, claims.SessionID, tokenStr) {
			return c.Status(http.StatusUnauthorized).JSON(responses.ErrorResponse{
				Success: false,
				Message: "Token has been invalidated",
			})
		}

		c.Locals("user_id", claims.UserID)
		c.Locals("session_id", claims.SessionID)

		return c.Next()
	}
}
