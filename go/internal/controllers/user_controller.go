package controllers

import (
	"context"
	"net/http"

	"github.com/gin-gonic/gin"
	"go-project/internal/dtos/requests"
	"go-project/internal/dtos/responses"
	"go-project/internal/helpers"
	"go-project/internal/services"
)

type UserController struct {
	userService services.UserServiceInterface
	jwtService  services.JWTServiceInterface
}

func NewUserController(userService services.UserServiceInterface, jwtService services.JWTServiceInterface) *UserController {
	return &UserController{userService: userService, jwtService: jwtService}
}

func (ctrl *UserController) Register(c *gin.Context) {
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

func (ctrl *UserController) Login(c *gin.Context) {
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

func (ctrl *UserController) UpdateUser(c *gin.Context) {
	userID := c.GetUint("user_id")
	var req requests.UpdateUserRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, responses.ErrorResponse{
			Success: false,
			Message: "Validation failed",
			Errors:  helpers.TranslateError(err),
		})
		return
	}
	res, err := ctrl.userService.UpdateUser(userID, &req)
	if err != nil {
		c.JSON(http.StatusInternalServerError, responses.ErrorResponse{
			Success: false,
			Message: err.Error(),
		})
		return
	}
	c.JSON(http.StatusOK, responses.SuccessResponse{
		Success: true,
		Message: "User updated successfully",
		Data:    res,
	})
}

func (ctrl *UserController) DeleteUser(c *gin.Context) {
	userID := c.GetUint("user_id")
	res, err := ctrl.userService.DeleteUser(userID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, responses.ErrorResponse{
			Success: false,
			Message: err.Error(),
		})
		return
	}
	c.JSON(http.StatusOK, responses.SuccessResponse{
		Success: true,
		Message: "User deleted successfully",
		Data:    res,
	})
}

func (ctrl *UserController) Logout(c *gin.Context) {
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
