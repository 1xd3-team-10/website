<?php
require_once __DIR__ . "/../lib/auth/auth.php";
require_once __DIR__ . "/../lib/connect.php";

if (!isLoggedIn()) {
    header("Location: ../login/index.php");
    exit;
}

$userId = getUserId();

$requestedUser = trim($_POST["contact_search"]);

if ($requestedUser == "") {
    header("Location: ../social/index.php?error=No%20User%20Entered");
    exit;
}

$conn = connect();

$stmt = $conn->prepare("SELECT * FROM users WHERE username=?;");
$stmt->bind_param("s", $requestedUser);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header("Location: ../social/index.php?error=User%20Not%20Found");
    exit;
}

if ($res->num_rows > 1) {
    header("Location: ../social/index.php?error=Internal%20Server%20Error");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id=?;");
$stmt->bind_param("i", $userId);
$stmt->execute();

$res2 = $stmt->get_result();

if ($res2->num_rows === 0) {
    header("Location: ../social/index.php?error=Internal%20Server%20Error");
    exit;
}

$user = $res2->fetch_assoc();
$contacts = json_decode($user["contacts"]);

if (!is_array($contacts)) {
    // if we get here, something is actually very wrong but ez fix
    $contacts = [];
}

$wantedContact = $res->fetch_assoc()["user_id"];

array_push($contacts, $wantedContact);
$newContacts = json_encode($contacts);

$stmt = $conn->prepare("UPDATE users SET contacts=? WHERE user_id = ?;");
$stmt->bind_param("si", $newContacts, $userId);
$stmt->execute();

header("Location: ../social/index.php");