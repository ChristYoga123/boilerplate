package controllers

import (
	"net/http"

	"github.com/gofiber/fiber/v3"
	"go-project/internal/dtos/requests"
	"go-project/internal/dtos/responses"
	"go-project/internal/helpers"
	"go-project/internal/services"
)

type UserController struct {
	userService services.UserServiceInterface
}

func NewUserController(userService services.UserServiceInterface) *UserController {
	return &UserController{userService: userService}
}

func (ctrl *UserController) UpdateUser(c fiber.Ctx) error {
	userID, ok := c.Locals("user_id").(uint)
	if !ok {
		return c.Status(http.StatusUnauthorized).JSON(responses.ErrorResponse{
			Success: false,
			Message: "Invalid token user",
		})
	}

	var req requests.UpdateUserRequest
	if err := c.Bind().Body(&req); err != nil {
		return c.Status(http.StatusBadRequest).JSON(responses.ErrorResponse{
			Success: false,
			Message: "Validation failed",
			Errors:  map[string]string{"error": "Invalid request body"},
		})
	}
	if err := helpers.ValidateStruct(&req); err != nil {
		return c.Status(http.StatusBadRequest).JSON(responses.ErrorResponse{
			Success: false,
			Message: "Validation failed",
			Errors:  helpers.TranslateError(err),
		})
	}

	res, err := ctrl.userService.UpdateUser(userID, &req)
	if err != nil {
		if err.Error() == "user not found" {
			return c.Status(http.StatusNotFound).JSON(responses.ErrorResponse{
				Success: false,
				Message: "User not found",
			})
		}
		return c.Status(http.StatusInternalServerError).JSON(responses.ErrorResponse{
			Success: false,
			Message: err.Error(),
		})
	}

	return c.Status(http.StatusOK).JSON(responses.SuccessResponse{
		Success: true,
		Message: "User updated successfully",
		Data:    res,
	})
}

func (ctrl *UserController) DeleteUser(c fiber.Ctx) error {
	userID, ok := c.Locals("user_id").(uint)
	if !ok {
		return c.Status(http.StatusUnauthorized).JSON(responses.ErrorResponse{
			Success: false,
			Message: "Invalid token user",
		})
	}

	res, err := ctrl.userService.DeleteUser(userID)
	if err != nil {
		if err.Error() == "user not found" {
			return c.Status(http.StatusNotFound).JSON(responses.ErrorResponse{
				Success: false,
				Message: "User not found",
			})
		}
		return c.Status(http.StatusInternalServerError).JSON(responses.ErrorResponse{
			Success: false,
			Message: err.Error(),
		})
	}

	return c.Status(http.StatusOK).JSON(responses.SuccessResponse{
		Success: true,
		Message: "User deleted successfully",
		Data:    res,
	})
}
