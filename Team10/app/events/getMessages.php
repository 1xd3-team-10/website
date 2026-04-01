<?php
require_once __DIR__ . "/../lib/auth/auth.php";
require_once __DIR__ . "/../lib/connect.php";

if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$recipient = $data["recipient"];
$senderID = getUserId();

if (!$recipient || $recipient == "") {
    http_response_code(400);
    echo json_encode(["error" => "Please select a valid recipient"]);
    exit;
}

$conn = connect();

$sql = "SELECT * FROM users WHERE username=?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $recipient);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Internal server error"]);
    exit;
}

$recipientID = $res->fetch_assoc()["user_id"];

if ($recipientID === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Internal server error"]);
    exit;
}

$last_id = $data['last_id'] ?? PHP_INT_MAX;

$convID = min($senderID, $recipientID) . '-' . max($senderID, $recipientID);

$sql = "SELECT *
FROM messages
WHERE conversation_id = ?
AND message_id < ?
ORDER BY message_id DESC
LIMIT 50;";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $convID, $last_id);
$stmt->execute();
$res = $stmt->get_result();
$messages = $res->fetch_all(MYSQLI_ASSOC);
$messages = array_reverse($messages);

$sql = "DELETE FROM updates WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();

echo json_encode(["messages" => $messages, "recipientID" => $recipientID]);