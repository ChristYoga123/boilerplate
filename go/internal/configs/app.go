package configs

import (
	"os"
	"strconv"
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
		AppName:        getEnv("APP_NAME", "Go Project"),
		DBName:         getEnv("DB_NAME", "go_project"),
		AppEnv:         getEnv("APP_ENV", "local"),
		DBDriver:       getEnv("DB_DRIVER", "sqlite"),
		DBDsn:          getEnv("DB_DSN", "database.db"),
		DBHost:         getEnv("DB_HOST", ""),
		DBUser:         getEnv("DB_USER", ""),
		DBPassword:     getEnv("DB_PASSWORD", ""),
		JWTSecret:      getEnv("JWT_SECRET", ""),
		RedisAddr:      getEnv("REDIS_ADDR", "localhost:6379"),
		RedisPassword:  getEnv("REDIS_PASSWORD", ""),
	}
}

func getEnv(key string, fallback string) string {
	value := os.Getenv(key)
	if value == "" {
		return fallback
	}
	return value
}
