package middlewares

import (
	"context"
	"net/http"
	"strings"

	"github.com/gin-gonic/gin"
	"go-project/internal/dtos/responses"
	"go-project/internal/services"
)

func AuthMiddleware(jwtSvc services.JWTServiceInterface) gin.HandlerFunc {
	return func(c *gin.Context) {
		authHeader := c.GetHeader("Authorization")
		if authHeader == "" {
			c.AbortWithStatusJSON(http.StatusUnauthorized, responses.ErrorResponse{
				Success: false,
				Message: "Authorization header required",
			})
			return
		}
		parts := strings.SplitN(authHeader, " ", 2)
		if len(parts) != 2 || parts[0] != "Bearer" {
			c.AbortWithStatusJSON(http.StatusUnauthorized, responses.ErrorResponse{
				Success: false,
				Message: "Invalid authorization format",
			})
			return
		}
		tokenStr := parts[1]
		userID, err := jwtSvc.ValidateToken(tokenStr)
		if err != nil {
			c.AbortWithStatusJSON(http.StatusUnauthorized, responses.ErrorResponse{
				Success: false,
				Message: "Invalid token",
			})
			return
		}
		if !jwtSvc.IsTokenActive(context.Background(), userID, tokenStr) {
			c.AbortWithStatusJSON(http.StatusUnauthorized, responses.ErrorResponse{
				Success: false,
				Message: "Token has been invalidated",
			})
			return
		}
		c.Set("user_id", userID)
		c.Next()
	}
}
