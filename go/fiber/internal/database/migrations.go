package database

import (
	"go-project/internal/dtos/models"

	"gorm.io/gorm"
)

func AutoMigrate(db *gorm.DB) error {
	return db.AutoMigrate(&models.User{})
}
