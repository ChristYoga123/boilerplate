package controllers

import (
	"context"
	"net/http"

	"github.com/gin-gonic/gin"
	"go-project/internal/dtos/requests"
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
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	res, err := ctrl.userService.Register(&req)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusCreated, res)
}

func (ctrl *UserController) Login(c *gin.Context) {
	var req requests.LoginRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	res, err := ctrl.userService.Login(&req)
	if err != nil {
		c.JSON(http.StatusUnauthorized, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, res)
}

func (ctrl *UserController) UpdateUser(c *gin.Context) {
	userID := c.GetUint("user_id")
	var req requests.UpdateUserRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	res, err := ctrl.userService.UpdateUser(userID, &req)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, res)
}

func (ctrl *UserController) DeleteUser(c *gin.Context) {
	userID := c.GetUint("user_id")
	res, err := ctrl.userService.DeleteUser(userID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, res)
}

func (ctrl *UserController) Logout(c *gin.Context) {
	userID := c.GetUint("user_id")
	if err := ctrl.jwtService.InvalidateToken(context.Background(), userID); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "logged out successfully"})
}
