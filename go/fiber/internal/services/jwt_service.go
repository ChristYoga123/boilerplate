package services

import (
	"context"
	"crypto/rand"
	"encoding/hex"
	"fmt"
	"time"

	"github.com/golang-jwt/jwt/v5"
	"github.com/redis/go-redis/v9"
	"go-project/internal/configs"
)

type JWTServiceInterface interface {
	GenerateToken(ctx context.Context, userID uint) (string, error)
	RefreshToken(ctx context.Context, userID uint, sessionID string) (string, error)
	ValidateToken(tokenStr string) (*Claims, error)
	InvalidateToken(ctx context.Context, userID uint, sessionID string) error
	IsTokenActive(ctx context.Context, userID uint, sessionID, tokenStr string) bool
}

type JWTService struct {
	cfg *configs.AppConfig
	rdb *redis.Client
}

func NewJWTService(cfg *configs.AppConfig, rdb *redis.Client) *JWTService {
	return &JWTService{cfg: cfg, rdb: rdb}
}

type Claims struct {
	UserID    uint   `json:"user_id"`
	SessionID string `json:"sid"`
	jwt.RegisteredClaims
}

func (s *JWTService) GenerateToken(ctx context.Context, userID uint) (string, error) {
	sessionID, err := generateRandomID()
	if err != nil {
		return "", err
	}
	return s.issueToken(ctx, userID, sessionID)
}

func (s *JWTService) RefreshToken(ctx context.Context, userID uint, sessionID string) (string, error) {
	if sessionID == "" {
		return "", fmt.Errorf("session id is required")
	}
	return s.issueToken(ctx, userID, sessionID)
}

func (s *JWTService) issueToken(ctx context.Context, userID uint, sessionID string) (string, error) {
	expiry := time.Duration(s.cfg.JWTExpiryHours) * time.Hour
	tokenID, err := generateRandomID()
	if err != nil {
		return "", err
	}

	claims := &Claims{
		UserID:    userID,
		SessionID: sessionID,
		RegisteredClaims: jwt.RegisteredClaims{
			ExpiresAt: jwt.NewNumericDate(time.Now().Add(expiry)),
			IssuedAt:  jwt.NewNumericDate(time.Now()),
			ID:        tokenID,
		},
	}

	token := jwt.NewWithClaims(jwt.SigningMethodHS256, claims)
	tokenStr, err := token.SignedString([]byte(s.cfg.JWTSecret))
	if err != nil {
		return "", err
	}

	if err := s.rdb.Set(ctx, sessionKey(userID, sessionID), tokenStr, expiry).Err(); err != nil {
		return "", err
	}
	return tokenStr, nil
}

func (s *JWTService) ValidateToken(tokenStr string) (*Claims, error) {
	token, err := jwt.ParseWithClaims(tokenStr, &Claims{}, func(t *jwt.Token) (any, error) {
		if _, ok := t.Method.(*jwt.SigningMethodHMAC); !ok {
			return nil, fmt.Errorf("unexpected signing method: %v", t.Header["alg"])
		}
		return []byte(s.cfg.JWTSecret), nil
	})
	if err != nil {
		return nil, err
	}

	claims, ok := token.Claims.(*Claims)
	if !ok || !token.Valid {
		return nil, fmt.Errorf("invalid token")
	}
	if claims.SessionID == "" || claims.ID == "" {
		return nil, fmt.Errorf("invalid token claims")
	}
	return claims, nil
}

func (s *JWTService) InvalidateToken(ctx context.Context, userID uint, sessionID string) error {
	return s.rdb.Del(ctx, sessionKey(userID, sessionID)).Err()
}

func (s *JWTService) IsTokenActive(ctx context.Context, userID uint, sessionID, tokenStr string) bool {
	stored, err := s.rdb.Get(ctx, sessionKey(userID, sessionID)).Result()
	if err != nil {
		return false
	}
	return stored == tokenStr
}

func sessionKey(userID uint, sessionID string) string {
	return fmt.Sprintf("jwt:user:%d:session:%s", userID, sessionID)
}

func generateRandomID() (string, error) {
	b := make([]byte, 16)
	if _, err := rand.Read(b); err != nil {
		return "", fmt.Errorf("failed to generate random id: %w", err)
	}
	return hex.EncodeToString(b), nil
}
