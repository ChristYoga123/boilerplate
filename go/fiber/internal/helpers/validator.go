package helpers

import (
	"fmt"
	"strings"

	"github.com/go-playground/validator/v10"
	"gorm.io/gorm"
)

var validate = validator.New()

func ValidateStruct(v any) error {
	return validate.Struct(v)
}

func TranslateError(err error) map[string]string {
	errorsMap := make(map[string]string)

	if validationErrors, ok := err.(validator.ValidationErrors); ok {
		for _, fe := range validationErrors {
			field := fe.Field()
			switch fe.Tag() {
			case "required":
				errorsMap[field] = fmt.Sprintf("%s is required", field)
			case "email":
				errorsMap[field] = "Invalid email format"
			case "min":
				errorsMap[field] = fmt.Sprintf("%s must be at least %s characters", field, fe.Param())
			case "max":
				errorsMap[field] = fmt.Sprintf("%s must be at most %s characters", field, fe.Param())
			case "numeric":
				errorsMap[field] = fmt.Sprintf("%s must be a number", field)
			default:
				errorsMap[field] = "Invalid value"
			}
		}
		return errorsMap
	}

	if err == gorm.ErrRecordNotFound {
		errorsMap["error"] = "Record not found"
		return errorsMap
	}

	if IsDuplicateError(err) {
		if strings.Contains(err.Error(), "username") {
			errorsMap["Username"] = "Username already exists"
		}
		if strings.Contains(err.Error(), "email") {
			errorsMap["Email"] = "Email already exists"
		}
		if len(errorsMap) == 0 {
			errorsMap["error"] = "Data already exists"
		}
		return errorsMap
	}

	errorsMap["error"] = err.Error()
	return errorsMap
}

func IsDuplicateError(err error) bool {
	if err == nil {
		return false
	}
	msg := err.Error()
	return strings.Contains(msg, "Duplicate entry") ||
		strings.Contains(msg, "UNIQUE constraint failed") ||
		strings.Contains(msg, "duplicate key value violates unique constraint")
}
