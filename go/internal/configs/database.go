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
		dialector = sqlite.Open(c.DBDsn)
	case "mysql":
		dialector = mysql.Open(c.DBDsn)
	case "postgres":
		dialector = postgres.Open(c.DBDsn)
	default:
		return nil, fmt.Errorf("invalid database driver: %s", c.DBDriver)
	}

	db, err := gorm.Open(dialector, &gorm.Config{})
	if err != nil {
		return nil, fmt.Errorf("failed to connect to database: %w", err)
	}

	return db, nil
}
