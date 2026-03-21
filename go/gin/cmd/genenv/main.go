package main

import (
	"bufio"
	"crypto/rand"
	"encoding/hex"
	"fmt"
	"os"
	"strings"
)

func main() {
	envFile := ".env"
	if len(os.Args) > 1 {
		envFile = os.Args[1]
	}

	secret, err := generateSecret(32)
	if err != nil {
		fmt.Fprintf(os.Stderr, "error generating secret: %v\n", err)
		os.Exit(1)
	}

	if err := updateEnvFile(envFile, "JWT_SECRET", secret); err != nil {
		fmt.Fprintf(os.Stderr, "error updating %s: %v\n", envFile, err)
		os.Exit(1)
	}

	fmt.Printf("JWT_SECRET updated in %s\n", envFile)
	fmt.Printf("JWT_SECRET=%s\n", secret)
}

func generateSecret(length int) (string, error) {
	b := make([]byte, length)
	if _, err := rand.Read(b); err != nil {
		return "", err
	}
	return hex.EncodeToString(b), nil
}

func updateEnvFile(path, key, value string) error {
	data, err := os.ReadFile(path)
	if err != nil && !os.IsNotExist(err) {
		return err
	}

	var lines []string
	found := false
	newLine := fmt.Sprintf("%s=%s", key, value)

	if len(data) > 0 {
		scanner := bufio.NewScanner(strings.NewReader(string(data)))
		for scanner.Scan() {
			line := scanner.Text()
			if strings.HasPrefix(line, key+"=") {
				lines = append(lines, newLine)
				found = true
			} else {
				lines = append(lines, line)
			}
		}
	}

	if !found {
		lines = append(lines, newLine)
	}

	content := strings.Join(lines, "\n")
	if !strings.HasSuffix(content, "\n") {
		content += "\n"
	}

	return os.WriteFile(path, []byte(content), 0644)
}
