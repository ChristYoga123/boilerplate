package main

import (
	"context"
	"fmt"
	"log"
	"net/http"
	"os"
	"os/signal"
	"syscall"
	"time"

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

	srv := &http.Server{
		Addr:    fmt.Sprintf(":%d", cfg.AppPort),
		Handler: router,
	}

	go func() {
		if err := srv.ListenAndServe(); err != nil && err != http.ErrServerClosed {
			log.Fatalf("failed to start server: %v", err)
		}
	}()

	quit := make(chan os.Signal, 1)
	signal.Notify(quit, syscall.SIGINT, syscall.SIGTERM)
	<-quit

	log.Println("shutting down server...")

	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()

	if err := srv.Shutdown(ctx); err != nil {
		log.Fatalf("server forced to shutdown: %v", err)
	}

	log.Println("server exited")
}
