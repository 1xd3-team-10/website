<?php
require_once __DIR__ . "/../lib/auth/auth.php";
require_once __DIR__ . "/../lib/connect.php";

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
    <script src="../assets/js/contacts.js"></script>
</head>

<body>
    <?php
    if (isset($_GET["error"])) {
        $err = htmlspecialchars($_GET["error"]);
        if ($err != "") {
            echo "<div onclick=\"this.style.display = 'none'\" class='auth_error'>" . $err . "</div>";
        }
    }
    ?>
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
            <div class="socialGrid">
                <card class="contacts">
                    <h2>
                        Contacts
                    </h2>
                    <form action="../events/addContact.php" method="post">
                        <div>
                            <label class="auth_input_label" for="contact_search">Search Contacts</label>
                            <div class="inputRow">
                                <input class="auth_input" id="contact_search" name="contact_search" type="text">
                                <input class="submit btn" type="submit" name="add_contact_submit"
                                    id="add_contact_submit" value="Add">
                            </div>
                            <card class="contact_list">
                                <?php
                                $conn = connect();
                                $userId = getUserId();
                                $stmt = $conn->prepare("SELECT * FROM users WHERE user_id=?");
                                $stmt->bind_param("i", $userId);
                                $stmt->execute();
                                $res = $stmt->get_result();

                                if ($res->num_rows === 0 || $res->num_rows > 1) {
                                    header("./?error=Internal%20Server%20Error");
                                    exit;
                                }

                                $userInfo = $res->fetch_assoc();

                                $contacts = json_decode($userInfo["contacts"], true);

                                if (!is_array($contacts)) {
                                    header("./?error=Internal%20Server%20Error");
                                    exit;
                                }

                                $placeholders = implode(",", array_fill(0, count($contacts), '?'));
                                $sql = "SELECT * FROM users WHERE user_id IN ($placeholders)";
                                $stmt = $conn->prepare($sql);

                                $types = str_repeat("i", count($contacts));
                                $stmt->bind_param($types, ...$contacts);

                                $stmt->execute();

                                $res = $stmt->get_result();
                                while ($row = $res->fetch_assoc()) {
                                    print "<div class='btn' id='contact'>" . $row["username"] . "</div>";
                                }
                                ?>
                            </card>
                        </div>
                    </form>
                </card>
                <card class="chat_window">
                    <card class="chat" id="chatWindow">

                    </card>
                    <div class="inputRow mt-16" method="post" action="../events/sendMessage.php">
                        <input class="auth_input" type="text" id="send_message_input" disabled name="send_message_input">
                        <button class="btn" id="sendMessage" type="submit">Send</button>
                    </div>
                </card>
            </div>
        </div>
    </div>
</body>

</html>