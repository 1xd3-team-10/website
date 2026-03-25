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
            <label for="username">Username</label>
            <input name="username" id="username" type="text">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" type="text">
            <label for="fullName">Full Name</label>
            <input type="text" name="fullName" id="fullName">
            <label for="password">Password</label>
            <input type="password" name="password" id="password">
            <label for="confirmPassword">Confirm Password</label>
            <input type="password" name="confirmPassword" id="confirmPassword">
            <input type="submit" name="submit" id="submit" value="Register">
        </form>
    </div>
</body>

</html>