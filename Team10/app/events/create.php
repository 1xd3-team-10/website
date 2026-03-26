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

$title = trim($_POST["title"] ?? "");
$description = trim($_POST["description"] ?? "");
$location = trim($_POST["location"] ?? "");
$startTime = $_POST["start_time"] ?? "";
$endTime = $_POST["end_time"] ?? "";

if ($title === "" || $startTime === "" || $endTime === "") {
    header("Location: ../index.php");
    exit;
}

$conn = connect();

$sql = "
    INSERT INTO events (user_id, title, description, location, start_time, end_time, created_at)
    VALUES (?, ?, ?, ?, ?, ?, NOW())
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssss", $userId, $title, $description, $location, $startTime, $endTime);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: ../index.php");
exit;