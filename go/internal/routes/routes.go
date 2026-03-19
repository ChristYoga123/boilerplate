package routes

import (
	"github.com/gin-gonic/gin"
	"go-project/internal/controllers"
	"go-project/internal/middlewares"
	"go-project/internal/services"
)

func SetupRoutes(r *gin.Engine, userCtrl *controllers.UserController, jwtSvc services.JWTServiceInterface) {
	api := r.Group("/api/v1")

	auth := api.Group("/auth")
	{
		auth.POST("/register", userCtrl.Register)
		auth.POST("/login", userCtrl.Login)
	}

	user := api.Group("/users")
	user.Use(middlewares.AuthMiddleware(jwtSvc))
	{
		user.PUT("/me", userCtrl.UpdateUser)
		user.DELETE("/me", userCtrl.DeleteUser)
		user.POST("/logout", userCtrl.Logout)
	}
}
