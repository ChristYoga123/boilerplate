package configs

import (
	"fmt"

	"gorm.io/driver/mysql"
	"gorm.io/driver/postgres"
	"gorm.io/driver/sqlite"
	"gorm.io/gorm"
)

func NewDatabase(c *AppConfig) (*gorm.DB, error) {
	var dialector gorm.Dialector

	switch c.DBDriver {
	case "sqlite":
		dsn := c.DBDsn
		if dsn == "" {
			dsn = "database.db"
		}
		dialector = sqlite.Open(dsn)
	case "mysql":
		dsn := c.DBDsn
		if dsn == "" {
			dsn = fmt.Sprintf("%s:%s@tcp(%s:%d)/%s?charset=utf8mb4&parseTime=True&loc=Local",
				c.DBUser, c.DBPassword, c.DBHost, c.DBPort, c.DBName)
		}
		dialector = mysql.Open(dsn)
	case "postgres":
		dsn := c.DBDsn
		if dsn == "" {
			dsn = fmt.Sprintf("host=%s user=%s password=%s dbname=%s port=%d sslmode=disable",
				c.DBHost, c.DBUser, c.DBPassword, c.DBName, c.DBPort)
		}
		dialector = postgres.Open(dsn)
	default:
		return nil, fmt.Errorf("invalid database driver: %s", c.DBDriver)
	}

	db, err := gorm.Open(dialector, &gorm.Config{})
	if err != nil {
		return nil, fmt.Errorf("failed to connect to database: %w", err)
	}

	return db, nil
}
