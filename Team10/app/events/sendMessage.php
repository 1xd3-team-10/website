<?php
require_once __DIR__ . "/../lib/auth/auth.php";
require_once __DIR__ . "/../lib/connect.php";


if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$msg = $data["msg"];
$recipient = $data["recipient"];
$senderID = getUserId();

if (!$msg || $msg == "") {
    http_response_code(400);
    echo json_encode(["error" => "Please send a valid message"]);
    exit;
}

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

$convID = min($senderID, $recipientID) . '-' . max($senderID, $recipientID);

$sql = "INSERT INTO messages (conversation_id, sender_id, content) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sis", $convID, $senderID, $msg);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Database error"]);
    exit;
}

$sql = "INSERT INTO updates (user_id, content) VALUES (?, ?);";
$stmt = $conn->prepare($sql);

$jsonContent = json_encode([
    "type" => "message",
    "data" => [
        "conversation_id" => $convID,
        "sender_id" => $senderID,
        "content" => $msg,
        "created_at" => date("Y-m-d H:i:s"),
    ]
]);

$stmt->bind_param("is", $recipientID, $jsonContent);
$stmt->execute();

http_response_code(201);
echo json_encode([
    "success" => true,
    "conversation_id" => $convID
]);

$stmt->close();
$conn->close();