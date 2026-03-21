package configs

import (
	"context"
	"fmt"

	"github.com/redis/go-redis/v9"
)

func NewRedis(c *AppConfig) (*redis.Client, error) {
	client := redis.NewClient(&redis.Options{
		Addr:     c.RedisAddr,
		Password: c.RedisPassword,
		DB:       c.RedisDB,
	})
	if err := client.Ping(context.Background()).Err(); err != nil {
		return nil, fmt.Errorf("failed to connect to redis: %w", err)
	}
	return client, nil
}
