<?php
require_once __DIR__ . "/../lib/auth/auth.php";
require_once __DIR__ . "/../lib/connect.php";

if (!isLoggedIn()) {
    header("Location: ../login/index.php");
    exit;
}

$userId = getUserId();

if ($userId === null) {
    header("Location: ../logout/index.php");
    exit;
}

$conn = connect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $eventId = $_POST["event_id"] ?? "";
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $startTime = $_POST["start_time"] ?? "";
    $endTime = $_POST["end_time"] ?? "";

    if ($eventId !== "" && $title !== "" && $startTime !== "" && $endTime !== "") {
        $sql = "
            UPDATE events
            SET title = ?, description = ?, location = ?, start_time = ?, end_time = ?
            WHERE event_id = ? AND user_id = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssii",
            $title,
            $description,
            $location,
            $startTime,
            $endTime,
            $eventId,
            $userId
        );
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
    header("Location: ../index.php");
    exit;
}

$eventId = $_GET["id"] ?? "";

$sql = "
    SELECT event_id, title, description, location, start_time, end_time
    FROM events
    WHERE event_id = ? AND user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $eventId, $userId);
$stmt->execute();

$res = $stmt->get_result();
$event = $res->fetch_assoc();

$stmt->close();
$conn->close();

if (!$event) {
    header("Location: ../index.php");
    exit;
}

$startValue = date("Y-m-d\TH:i", strtotime($event["start_time"]));
$endValue = date("Y-m-d\TH:i", strtotime($event["end_time"]));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="../style.css">
    <script src="../assets/js/eventMod.js"></script>
</head>

<body>
    <div class="container centerHorizontalItems centerVerticalItems">
        <div class="authCard appShell">
            <card>
                <h1>Edit Event</h1>
                <subtext>Update the details of your appointment.</subtext>

                <form method="post" action="edit.php">
                    <input type="hidden" name="event_id" value="<?= htmlspecialchars($event["event_id"]) ?>">

                    <label class="auth_input_label" for="title">Title</label>
                    <input class="auth_input" type="text" id="title" name="title"
                        value="<?= htmlspecialchars($event["title"]) ?>" required>

                    <label class="auth_input_label" for="description">Description</label>
                    <textarea class="auth_input appTextarea" id="description"
                        name="description"><?= htmlspecialchars($event["description"]) ?></textarea>

                    <label class="auth_input_label" for="location">Location</label>
                    <input class="auth_input" type="text" id="location" name="location"
                        value="<?= htmlspecialchars($event["location"]) ?>">

                    <label class="auth_input_label" for="start_time">Start Time</label>
                    <input class="auth_input" type="datetime-local" id="start_time" name="start_time"
                        value="<?= htmlspecialchars($startValue) ?>" required>

                    <label class="auth_input_label" for="end_time">End Time</label>
                    <input class="auth_input" type="datetime-local" id="end_time" name="end_time"
                        value="<?= htmlspecialchars($endValue) ?>" required>
                    <div class="inputRow">
                        <button class="appSubmit btn" type="button" onclick="deleteConfirmation(this, <?= (int) $event['event_id'] ?>)" class="btn">Delete</button>
                        <input class="appSubmit btn" type="submit" value="Save Changes">
                    </div>
                </form>

                <p class="backLink"><a href="../index.php">Back to dashboard</a></p>
            </card>
        </div>
    </div>
</body>

</html>