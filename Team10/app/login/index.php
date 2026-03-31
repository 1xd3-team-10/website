<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/Team10/style.css">
	<link rel="stylesheet" href="/Team10/app/style.css">
	<title>Login</title>
</head>
<body>
	<?php
    require_once __DIR__ . "/../lib/auth/login.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $login = handleLogin($_POST["username"], $_POST["password"]);

        if ($login !== "ok") {
            echo "<div onclick=\"this.style.display = 'none'\" class='auth_error'>" . $login . "</div>";
        } else {
			header("Location: /Team10/app/index.php", true, 301);
			exit();
		}
    }
    ?>
	<div class="container centerHorizontalItems centerVerticalItems">
        <form action="/Team10/app/login/index.php" method="post" id="registrationForm" class="authCard">
            <label class="auth_input_label" for="username">Username</label>
            <input placeholder="username..." class="auth_input" name="username" id="username" type="text">
            <label class="auth_input_label" for="password">Password</label>
            <input placeholder="password..." class="auth_input" type="password" name="password" id="password">
            <input class="auth_input" type="submit" name="submit" id="submit" value="Login">
        </form>
    </div>
</body>
</html>
