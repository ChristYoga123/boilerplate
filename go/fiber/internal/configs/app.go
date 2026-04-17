package configs

import (
	"fmt"
	"os"
	"strconv"
	"strings"
)

type AppConfig struct {
	AppPort        int
	DBPort         int
	JWTExpiryHours int
	RedisDB        int
	AppName        string
	AppEnv         string
	DBDriver       string
	DBHost         string
	DBDsn          string
	DBName         string
	DBUser         string
	DBPassword     string
	JWTSecret      string
	RedisAddr      string
	RedisPassword  string
}

func NewAppConfig() *AppConfig {
	appPort, _ := strconv.Atoi(getEnv("APP_PORT", "8080"))
	dbPort, _ := strconv.Atoi(getEnv("DB_PORT", "3306"))
	jwtExpiryHours, _ := strconv.Atoi(getEnv("JWT_EXPIRY_HOURS", "24"))
	redisDB, _ := strconv.Atoi(getEnv("REDIS_DB", "0"))

	return &AppConfig{
		AppPort:        appPort,
		DBPort:         dbPort,
		JWTExpiryHours: jwtExpiryHours,
		RedisDB:        redisDB,
		AppName:        getEnv("APP_NAME", "Go Project Fiber"),
		DBName:         getEnv("DB_NAME", "go_project"),
		AppEnv:         getEnv("APP_ENV", "local"),
		DBDriver:       getEnv("DB_DRIVER", "sqlite"),
		DBDsn:          getEnv("DB_DSN", ""),
		DBHost:         getEnv("DB_HOST", ""),
		DBUser:         getEnv("DB_USER", ""),
		DBPassword:     getEnv("DB_PASSWORD", ""),
		JWTSecret:      getEnv("JWT_SECRET", ""),
		RedisAddr:      getEnv("REDIS_ADDR", "localhost:6379"),
		RedisPassword:  getEnv("REDIS_PASSWORD", ""),
	}
}

func (c *AppConfig) Validate() error {
	if c.AppPort <= 0 {
		return fmt.Errorf("APP_PORT must be greater than 0")
	}
	if c.JWTExpiryHours <= 0 {
		return fmt.Errorf("JWT_EXPIRY_HOURS must be greater than 0")
	}
	if strings.TrimSpace(c.JWTSecret) == "" {
		return fmt.Errorf("JWT_SECRET is required")
	}
	switch c.DBDriver {
	case "sqlite", "mysql", "postgres":
	default:
		return fmt.Errorf("DB_DRIVER must be one of sqlite, mysql, or postgres")
	}
	return nil
}

func getEnv(key string, fallback string) string {
	value := os.Getenv(key)
	if value == "" {
		return fallback
	}
	return value
}
