package controllers

import (
	"context"
	"net/http"

	"go-project/internal/dtos/requests"
	"go-project/internal/dtos/responses"
	"go-project/internal/helpers"
	"go-project/internal/services"

	"github.com/gin-gonic/gin"
)

type AuthController struct {
	userService services.UserServiceInterface
	jwtService  services.JWTServiceInterface
}

func NewAuthController(userService services.UserServiceInterface, jwtService services.JWTServiceInterface) *AuthController {
	return &AuthController{userService: userService, jwtService: jwtService}
}

func (ctrl *AuthController) Register(c *gin.Context) {
	var req requests.RegisterRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, responses.ErrorResponse{
			Success: false,
			Message: "Validation failed",
			Errors:  helpers.TranslateError(err),
		})
		return
	}
	res, err := ctrl.userService.Register(&req)
	if err != nil {
		status := http.StatusInternalServerError
		if helpers.IsDuplicateError(err) {
			status = http.StatusConflict
		}
		c.JSON(status, responses.ErrorResponse{
			Success: false,
			Message: err.Error(),
			Errors:  helpers.TranslateError(err),
		})
		return
	}
	c.JSON(http.StatusCreated, responses.SuccessResponse{
		Success: true,
		Message: "User registered successfully",
		Data:    res,
	})
}

func (ctrl *AuthController) Login(c *gin.Context) {
	var req requests.LoginRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, responses.ErrorResponse{
			Success: false,
			Message: "Validation failed",
			Errors:  helpers.TranslateError(err),
		})
		return
	}
	res, err := ctrl.userService.Login(&req)
	if err != nil {
		c.JSON(http.StatusUnauthorized, responses.ErrorResponse{
			Success: false,
			Message: err.Error(),
		})
		return
	}
	c.JSON(http.StatusOK, responses.SuccessResponse{
		Success: true,
		Message: "Login successful",
		Data:    res,
	})
}

func (ctrl *AuthController) RefreshToken(c *gin.Context) {
	userID := c.GetUint("user_id")
	token, err := ctrl.jwtService.GenerateToken(userID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, responses.ErrorResponse{
			Success: false,
			Message: err.Error(),
		})
		return
	}
	c.JSON(http.StatusOK, responses.SuccessResponse{
		Success: true,
		Message: "Token refreshed successfully",
		Data:    gin.H{"token": token},
	})
}

func (ctrl *AuthController) Logout(c *gin.Context) {
	userID := c.GetUint("user_id")
	if err := ctrl.jwtService.InvalidateToken(context.Background(), userID); err != nil {
		c.JSON(http.StatusInternalServerError, responses.ErrorResponse{
			Success: false,
			Message: err.Error(),
		})
		return
	}
	c.JSON(http.StatusOK, responses.SuccessResponse{
		Success: true,
		Message: "Logged out successfully",
		Data:    nil,
	})
}
