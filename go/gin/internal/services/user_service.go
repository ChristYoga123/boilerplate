package services

import (
	"errors"
	"time"

	"go-project/internal/dtos/models"
	"go-project/internal/dtos/requests"
	"go-project/internal/dtos/responses"
	"go-project/internal/repositories"

	"golang.org/x/crypto/bcrypt"
)

type UserServiceInterface interface {
	Register(request *requests.RegisterRequest) (*responses.RegisterResponse, error)
	Login(request *requests.LoginRequest) (*responses.LoginResponse, error)
	UpdateUser(id uint, request *requests.UpdateUserRequest) (*responses.UpdateUserResponse, error)
	DeleteUser(id uint) (*responses.DeleteUserResponse, error)
}

type UserService struct {
	userRepository repositories.UserRepositoryInterface
	jwtService     JWTServiceInterface
}

func NewUserService(userRepository repositories.UserRepositoryInterface, jwtService JWTServiceInterface) *UserService {
	return &UserService{
		userRepository: userRepository,
		jwtService:     jwtService,
	}
}

func (s *UserService) Register(request *requests.RegisterRequest) (*responses.RegisterResponse, error) {
	hashed, err := bcrypt.GenerateFromPassword([]byte(request.Password), bcrypt.DefaultCost)
	if err != nil {
		return nil, err
	}
	user := &models.User{
		Username: request.Username,
		Email:    request.Email,
		Password: string(hashed),
	}
	if err := s.userRepository.CreateUser(user); err != nil {
		return nil, err
	}
	return &responses.RegisterResponse{
		ID:        user.ID,
		Username:  user.Username,
		Email:     user.Email,
		CreatedAt: user.CreatedAt,
		UpdatedAt: user.UpdatedAt,
	}, nil
}

func (s *UserService) Login(request *requests.LoginRequest) (*responses.LoginResponse, error) {
	user, err := s.userRepository.GetUserByEmail(request.Email)
	if err != nil {
		return nil, errors.New("invalid credentials")
	}
	if err := bcrypt.CompareHashAndPassword([]byte(user.Password), []byte(request.Password)); err != nil {
		return nil, errors.New("invalid credentials")
	}
	token, err := s.jwtService.GenerateToken(user.ID)
	if err != nil {
		return nil, err
	}
	return &responses.LoginResponse{Token: token}, nil
}

func (s *UserService) UpdateUser(id uint, request *requests.UpdateUserRequest) (*responses.UpdateUserResponse, error) {
	user, err := s.userRepository.GetUserByID(id)
	if err != nil {
		return nil, errors.New("user not found")
	}
	if request.Password != "" {
		hashed, err := bcrypt.GenerateFromPassword([]byte(request.Password), bcrypt.DefaultCost)
		if err != nil {
			return nil, err
		}
		user.Password = string(hashed)
	}
	user.Username = request.Username
	user.Email = request.Email
	if err := s.userRepository.UpdateUser(user); err != nil {
		return nil, err
	}
	return &responses.UpdateUserResponse{
		ID:        user.ID,
		Username:  user.Username,
		Email:     user.Email,
		UpdatedAt: user.UpdatedAt,
	}, nil
}

func (s *UserService) DeleteUser(id uint) (*responses.DeleteUserResponse, error) {
	user, err := s.userRepository.GetUserByID(id)
	if err != nil {
		return nil, errors.New("user not found")
	}
	if err := s.userRepository.DeleteUser(id); err != nil {
		return nil, err
	}
	return &responses.DeleteUserResponse{
		ID:        user.ID,
		Username:  user.Username,
		Email:     user.Email,
		DeletedAt: time.Now(),
	}, nil
}
