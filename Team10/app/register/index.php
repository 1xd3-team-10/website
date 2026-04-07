<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Team10/style.css">
    <link rel="stylesheet" href="/Team10/app/style.css">
    <title>Register</title>
</head>

<body>
    <?php
    require_once __DIR__ . "/../lib/auth/registration.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $registration = handleRegistration($_POST["username"], $_POST["email"], $_POST["fullName"], $_POST["password"], $_POST["confirmPassword"]);

        if ($registration !== "ok") {
            echo "<div onclick=\"this.style.display = 'none'\" class='auth_error'>" . $registration . "</div>";
        }
    }
    ?>
    <div class="container centerHorizontalItems centerVerticalItems">
        <form action="/Team10/app/register/index.php" method="post" id="registrationForm" class="authCard">
            <label class="auth_input_label" for="username">Username</label>
            <input class="auth_input" name="username" id="username" type="text">
            <label class="auth_input_label" for="email">Email</label>
            <input class="auth_input" type="email" id="email" name="email" type="text">
            <label class="auth_input_label" for="fullName">Full Name</label>
            <input class="auth_input" type="text" name="fullName" id="fullName">
            <label class="auth_input_label" for="password">Password</label>
            <input class="auth_input" type="password" name="password" id="password">
            <label class="auth_input_label" for="confirmPassword">Confirm Password</label>
            <input class="auth_input" type="password" name="confirmPassword" id="confirmPassword">
            <input class="auth_input" type="submit" name="submit" id="submit" value="Register">
            <a href="../login/index.php">Login</a>
        </form>
    </div>
</body>

</html>