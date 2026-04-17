package routes

import (
	"github.com/gofiber/fiber/v3"
	"github.com/redis/go-redis/v9"
	"go-project/internal/configs"
	"go-project/internal/controllers"
	"go-project/internal/middlewares"
	"go-project/internal/repositories"
	"go-project/internal/services"
	"gorm.io/gorm"
)

func SetupRoutes(app *fiber.App, cfg *configs.AppConfig, db *gorm.DB, rdb *redis.Client) {
	userRepo := repositories.NewUserRepository(db)
	jwtSvc := services.NewJWTService(cfg, rdb)
	userSvc := services.NewUserService(userRepo, jwtSvc)
	authCtrl := controllers.NewAuthController(userSvc, jwtSvc)
	userCtrl := controllers.NewUserController(userSvc)

	api := app.Group("/api/v1")

	auth := api.Group("/auth")
	auth.Post("/register", authCtrl.Register)
	auth.Post("/login", authCtrl.Login)
	auth.Post("/refresh", middlewares.AuthMiddleware(jwtSvc), authCtrl.RefreshToken)

	users := api.Group("/users", middlewares.AuthMiddleware(jwtSvc))
	users.Put("/me", userCtrl.UpdateUser)
	users.Delete("/me", userCtrl.DeleteUser)
	users.Post("/logout", authCtrl.Logout)
}
