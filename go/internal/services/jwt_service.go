package services

import (
	"context"
	"fmt"
	"time"

	"github.com/golang-jwt/jwt/v5"
	"github.com/redis/go-redis/v9"
	"go-project/internal/configs"
)

type JWTServiceInterface interface {
	GenerateToken(userID uint) (string, error)
	ValidateToken(tokenStr string) (uint, error)
	InvalidateToken(ctx context.Context, userID uint) error
	IsTokenActive(ctx context.Context, userID uint, tokenStr string) bool
}

type JWTService struct {
	cfg *configs.AppConfig
	rdb *redis.Client
}

func NewJWTService(cfg *configs.AppConfig, rdb *redis.Client) *JWTService {
	return &JWTService{cfg: cfg, rdb: rdb}
}

type Claims struct {
	UserID uint `json:"user_id"`
	jwt.RegisteredClaims
}

func (s *JWTService) GenerateToken(userID uint) (string, error) {
	expiry := time.Duration(s.cfg.JWTExpiryHours) * time.Hour
	claims := &Claims{
		UserID: userID,
		RegisteredClaims: jwt.RegisteredClaims{
			ExpiresAt: jwt.NewNumericDate(time.Now().Add(expiry)),
			IssuedAt:  jwt.NewNumericDate(time.Now()),
		},
	}
	token := jwt.NewWithClaims(jwt.SigningMethodHS256, claims)
	tokenStr, err := token.SignedString([]byte(s.cfg.JWTSecret))
	if err != nil {
		return "", err
	}

	key := fmt.Sprintf("jwt:user:%d", userID)
	if err := s.rdb.Set(context.Background(), key, tokenStr, expiry).Err(); err != nil {
		return "", err
	}
	return tokenStr, nil
}

func (s *JWTService) ValidateToken(tokenStr string) (uint, error) {
	token, err := jwt.ParseWithClaims(tokenStr, &Claims{}, func(t *jwt.Token) (interface{}, error) {
		if _, ok := t.Method.(*jwt.SigningMethodHMAC); !ok {
			return nil, fmt.Errorf("unexpected signing method: %v", t.Header["alg"])
		}
		return []byte(s.cfg.JWTSecret), nil
	})
	if err != nil {
		return 0, err
	}
	claims, ok := token.Claims.(*Claims)
	if !ok || !token.Valid {
		return 0, fmt.Errorf("invalid token")
	}
	return claims.UserID, nil
}

func (s *JWTService) InvalidateToken(ctx context.Context, userID uint) error {
	key := fmt.Sprintf("jwt:user:%d", userID)
	return s.rdb.Del(ctx, key).Err()
}

func (s *JWTService) IsTokenActive(ctx context.Context, userID uint, tokenStr string) bool {
	key := fmt.Sprintf("jwt:user:%d", userID)
	stored, err := s.rdb.Get(ctx, key).Result()
	if err != nil {
		return false
	}
	return stored == tokenStr
}
