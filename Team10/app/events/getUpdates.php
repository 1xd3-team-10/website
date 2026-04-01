<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/../lib/auth/auth.php";
require_once __DIR__ . "/../lib/connect.php";

if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$conn = connect();

$data = json_decode(file_get_contents("php://input"), true);

$seenUpdates = $data["seenUpdates"];

if (!is_array($seenUpdates) || $seenUpdates === []) {
    $seenUpdates = [-1];
}
$userId = getUserId();
$placeholders = implode(",", array_fill(0, count($seenUpdates),"?"));
$sql = "SELECT * FROM updates WHERE user_id=? AND update_id NOT IN ($placeholders)";
$stmt = $conn->prepare($sql);

$types = "i" . str_repeat("i", count($seenUpdates));
$stmt->bind_param($types, $userId, ...$seenUpdates);
$stmt->execute();
$res = $stmt->get_result();
$updates = $res->fetch_all(MYSQLI_ASSOC);

$sql = "DELETE FROM updates WHERE created_at < NOW() - INTERVAL 30 SECOND";
$stmt = $conn->prepare($sql);
$stmt->execute();

echo json_encode(["updates" => $updates]);