package models

import (
	"time"

	"gorm.io/plugin/soft_delete"
)

type User struct {
	ID        uint                  `json:"id" gorm:"primaryKey"`
	Username  string                `json:"username" gorm:"uniqueIndex:udx_users_username,composite:deleted_at"`
	Email     string                `json:"email" gorm:"uniqueIndex:udx_users_email,composite:deleted_at"`
	Password  string                `json:"password"`
	CreatedAt time.Time             `json:"created_at"`
	UpdatedAt time.Time             `json:"updated_at"`
	DeletedAt soft_delete.DeletedAt `json:"deleted_at" gorm:"softDelete:unix;uniqueIndex:udx_users_username,composite:deleted_at;uniqueIndex:udx_users_email,composite:deleted_at"`
}
