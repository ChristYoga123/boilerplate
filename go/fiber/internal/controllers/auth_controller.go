package controllers

import (
	"net/http"

	"github.com/gofiber/fiber/v3"
	"go-project/internal/dtos/requests"
	"go-project/internal/dtos/responses"
	"go-project/internal/helpers"
	"go-project/internal/services"
)

type AuthController struct {
	userService services.UserServiceInterface
	jwtService  services.JWTServiceInterface
}

func NewAuthController(userService services.UserServiceInterface, jwtService services.JWTServiceInterface) *AuthController {
	return &AuthController{userService: userService, jwtService: jwtService}
}

func (ctrl *AuthController) Register(c fiber.Ctx) error {
	var req requests.RegisterRequest
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

	res, err := ctrl.userService.Register(&req)
	if err != nil {
		status := http.StatusInternalServerError
		if helpers.IsDuplicateError(err) {
			status = http.StatusConflict
		}
		return c.Status(status).JSON(responses.ErrorResponse{
			Success: false,
			Message: err.Error(),
			Errors:  helpers.TranslateError(err),
		})
	}

	return c.Status(http.StatusCreated).JSON(responses.SuccessResponse{
		Success: true,
		Message: "User registered successfully",
		Data:    res,
	})
}

func (ctrl *AuthController) Login(c fiber.Ctx) error {
	var req requests.LoginRequest
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

	res, err := ctrl.userService.Login(c.Context(), &req)
	if err != nil {
		return c.Status(http.StatusUnauthorized).JSON(responses.ErrorResponse{
			Success: false,
			Message: err.Error(),
		})
	}

	return c.Status(http.StatusOK).JSON(responses.SuccessResponse{
		Success: true,
		Message: "Login successful",
		Data:    res,
	})
}

func (ctrl *AuthController) RefreshToken(c fiber.Ctx) error {
	userID, ok := c.Locals("user_id").(uint)
	if !ok {
		return c.Status(http.StatusUnauthorized).JSON(responses.ErrorResponse{
			Success: false,
			Message: "Invalid token user",
		})
	}
	sessionID, ok := getSessionID(c)
	if !ok {
		return c.Status(http.StatusUnauthorized).JSON(responses.ErrorResponse{
			Success: false,
			Message: "Invalid token session",
		})
	}

	token, err := ctrl.jwtService.RefreshToken(c.Context(), userID, sessionID)
	if err != nil {
		return c.Status(http.StatusInternalServerError).JSON(responses.ErrorResponse{
			Success: false,
			Message: err.Error(),
		})
	}

	return c.Status(http.StatusOK).JSON(responses.SuccessResponse{
		Success: true,
		Message: "Token refreshed successfully",
		Data:    responses.LoginResponse{Token: token},
	})
}

func (ctrl *AuthController) Logout(c fiber.Ctx) error {
	userID, ok := c.Locals("user_id").(uint)
	if !ok {
		return c.Status(http.StatusUnauthorized).JSON(responses.ErrorResponse{
			Success: false,
			Message: "Invalid token user",
		})
	}
	sessionID, ok := getSessionID(c)
	if !ok {
		return c.Status(http.StatusUnauthorized).JSON(responses.ErrorResponse{
			Success: false,
			Message: "Invalid token session",
		})
	}

	if err := ctrl.jwtService.InvalidateToken(c.Context(), userID, sessionID); err != nil {
		return c.Status(http.StatusInternalServerError).JSON(responses.ErrorResponse{
			Success: false,
			Message: err.Error(),
		})
	}

	return c.Status(http.StatusOK).JSON(responses.SuccessResponse{
		Success: true,
		Message: "Logged out successfully",
		Data:    nil,
	})
}

func getSessionID(c fiber.Ctx) (string, bool) {
	value, ok := c.Locals("session_id").(string)
	if !ok || value == "" {
		return "", false
	}
	return value, true
}
