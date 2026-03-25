<?php
    require_once __DIR__ . "/../connect.php";
    
    function handleRegistration($username, $email, $fullName, $password, $confirmPassword): string {
        if (strlen($username) > 64) return "username must be under 64 characters";
        elseif (strlen($username) < 8) return "username must be at least 8 characters";
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) return "email not formatted correctly";
        elseif (strlen($fullName) > 64) return "full name must be under 64 characters";
        elseif (strlen($fullName) < 8) return "full name must be at least 8 characters";
        elseif (strlen($password) > 128) return "password must be under 128 characters";
        elseif (strlen($password) < 8) return "password must be at least 8 characters";
        elseif (strtolower($password) == $password) return "password must contain at least one uppercase letter";
        elseif (strtoupper($password) == $password) return "password must contain at least one lowercase letter";
        elseif ($confirmPassword != $password) return "passwords do not match";

        $conn = connect();

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (username, password_hash, full_name, email) VALUES (?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssss", $username, $hashed_password, $fullName, $email);
            $stmt->execute();
        }
        $stmt->close();
        $conn->close();
        return "ok";
    }