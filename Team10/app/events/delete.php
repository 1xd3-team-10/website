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

$eventId = $_POST["event_id"] ?? "";

if ($eventId === "") {
    header("Location: ../index.php");
    exit;
}

$conn = connect();

$sql = "DELETE FROM events WHERE event_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $eventId, $userId);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: ../index.php");
exit;