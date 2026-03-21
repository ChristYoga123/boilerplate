package main

import (
	"fmt"
	"log"

	"github.com/gin-gonic/gin"
	"github.com/joho/godotenv"
	"go-project/internal/configs"
	"go-project/internal/controllers"
	"go-project/internal/dtos/models"
	"go-project/internal/repositories"
	"go-project/internal/routes"
	"go-project/internal/services"
)

func main() {
	if err := godotenv.Load(); err != nil {
		log.Println("no .env file found, using environment variables")
	}

	cfg := configs.NewAppConfig()

	db, err := configs.NewDatabase(cfg)
	if err != nil {
		log.Fatalf("failed to connect database: %v", err)
	}
	db.AutoMigrate(&models.User{})

	rdb, err := configs.NewRedis(cfg)
	if err != nil {
		log.Fatalf("failed to connect redis: %v", err)
	}

	userRepo := repositories.NewUserRepository(db)
	jwtSvc := services.NewJWTService(cfg, rdb)
	userSvc := services.NewUserService(userRepo, jwtSvc)
	authCtrl := controllers.NewAuthController(userSvc, jwtSvc)
	userCtrl := controllers.NewUserController(userSvc)

	router := gin.Default()
	routes.SetupRoutes(router, authCtrl, userCtrl, jwtSvc)

	addr := fmt.Sprintf(":%d", cfg.AppPort)
	if err := router.Run(addr); err != nil {
		log.Fatalf("failed to start server: %v", err)
	}
}
