package controllers

import (
	"net/http"

	"go-project/internal/dtos/requests"
	"go-project/internal/dtos/responses"
	"go-project/internal/helpers"
	"go-project/internal/services"

	"github.com/gin-gonic/gin"
)

type UserController struct {
	userService services.UserServiceInterface
}

func NewUserController(userService services.UserServiceInterface) *UserController {
	return &UserController{userService: userService}
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
		if err.Error() == "user not found" {
			c.JSON(http.StatusNotFound, responses.ErrorResponse{
				Success: false,
				Message: "User not found",
			})
			return
		}
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
		if err.Error() == "user not found" {
			c.JSON(http.StatusNotFound, responses.ErrorResponse{
				Success: false,
				Message: "User not found",
			})
			return
		}
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
