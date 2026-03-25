<?php
    session_start();

    require_once __DIR__ . "/../connect.php";

    function handleLogin($username, $password): string {
        $conn = connect();

        $sql = "SELECT * FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
        }

        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            $stmt->close();
            $conn->close();
            return "user does not exist";
        }

        if ($res->num_rows > 1) {
            $stmt->close();
            $conn->close();
            return "an internal server error has occured";
        }

        $user = $res->fetch_assoc();

        if (!password_verify($password, $user["password_hash"], )) {
            $stmt->close();
            $conn->close();
            return "user does not exist";
        }

        $stmt->close();
        $conn->close();

        $_SESSION["username"] = $user["username"];
        $_SESSION["fullName"] = $user["full_name"];
        $_SESSION["email"] = $user["email"];

        return "ok";
    }