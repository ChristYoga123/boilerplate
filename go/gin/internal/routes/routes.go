package routes

import (
	"github.com/gin-gonic/gin"
	"go-project/internal/controllers"
	"go-project/internal/middlewares"
	"go-project/internal/services"
)

func SetupRoutes(r *gin.Engine, authCtrl *controllers.AuthController, userCtrl *controllers.UserController, jwtSvc services.JWTServiceInterface) {
	api := r.Group("/api/v1")

	auth := api.Group("/auth")
	{
		auth.POST("/register", authCtrl.Register)
		auth.POST("/login", authCtrl.Login)
		auth.POST("/refresh", middlewares.AuthMiddleware(jwtSvc), authCtrl.RefreshToken)
	}

	user := api.Group("/users")
	user.Use(middlewares.AuthMiddleware(jwtSvc))
	{
		user.PUT("/me", userCtrl.UpdateUser)
		user.DELETE("/me", userCtrl.DeleteUser)
		user.POST("/logout", authCtrl.Logout)
	}
}
