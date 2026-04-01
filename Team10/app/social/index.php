<?php 
require_once __DIR__ . "/../lib/auth/auth.php";

if (!isLoggedIn()) {
    header("Location: ./login/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social</title>
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container centerHorizontalItems">
        <div class="authCard appShell">
            <header class="appHeader">
                <div>
                    <h1>Social Dashboard</h1>
                    <subtext>Manage your contacts and communications</subtext>
                </div>

                <links>
                    <a href="../logout/index.php">Log Out</a>
                </links>
			</header>
            <div class="appGrid">
                <card>
                    <h2>
                        Contacts
                    </h2>
                    <form action="../events/addContact.php" method="post">
                        <div>
                            <label class="auth_input_label" for="contact_search">Search Contacts</label>
                            <div class="inputRow">
                                <input class="auth_input" id="contact_search" name="contact_search" type="text">
                                <input class="submit btn" type="submit" name="add_contact_submit" id="add_contact_submit">
                            </div>
                        </div>
                    </form>
                </card>
            </div>
        </div>
    </div>
</body>
</html>