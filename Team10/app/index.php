<?php
require_once __DIR__ . "/lib/auth/auth.php";
require_once __DIR__ . "/lib/connect.php";

if (!isLoggedIn()) {
    header("Location: ./login/index.php");
    exit;
}

$userId = getUserId();

if ($userId === null) {
    header("Location: ./logout/index.php");
    exit;
}

$conn = connect();

$sql = "
    SELECT event_id, title, description, location, start_time, end_time
    FROM events
    WHERE user_id = ?
    ORDER BY start_time ASC
";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Failed to prepare statement.");
}
$stmt->bind_param("i", $userId);
$stmt->execute();

$res = $stmt->get_result();
$events = [];

while ($row = $res->fetch_assoc()) {
    $events[] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Scheduler</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style.css">
    <script src="assets/js/calendar.js"></script>
    <script src="assets/js/eventMod.js"></script>
</head>
<body>
    <div class="container centerHorizontalItems">
        <div class="authCard appShell">
            <header class="appHeader">
                <div>
                    <h1>Event Dashboard</h1>
                    <subtext>Manage your appointments and upcoming meetings.</subtext>
                </div>

                <links>
                    <a href="./logout/index.php">Log Out</a>
                </links>
			</header>

            <div class="appGrid">
                <card>
                    <h2>Create Event</h2>

                    <form action="events/create.php" method="post">
                        <label class="auth_input_label" for="title">Title</label>
                        <input class="auth_input" type="text" id="title" name="title" required>

                        <label class="auth_input_label" for="description">Description</label>
                        <textarea class="auth_input appTextarea" id="description" name="description"></textarea>

                        <label class="auth_input_label" for="location">Location</label>
                        <input class="auth_input" type="text" id="location" name="location">

                        <label class="auth_input_label" for="start_time">Start Time</label>
                        <input class="auth_input" type="datetime-local" id="start_time" name="start_time" required>

                        <label class="auth_input_label" for="end_time">End Time</label>
                        <input class="auth_input" type="datetime-local" id="end_time" name="end_time" required>

                        <input class="auth_input appSubmit btn" type="submit" value="Create Event">
                    </form>
                </card>
				<div class="calendarSection">
					<card>
						<div class="calendarTopBar">
							<h2>Calendar</h2>
						</div>

						<div id="calendarWrapper">
							<div id="calendar"></div>
						</div>
					</card>
				</div>
                <card>
                    <h2>Upcoming Events</h2>
                    <?php if (count($events) === 0): ?>
                        <subtext>No events yet.</subtext>
                    <?php else: ?>
                        <cardContainer class="eventList">
                            <?php foreach ($events as $event): ?>
                                <card class="eventCard">
                                    <h3><?= htmlspecialchars($event["title"]) ?></h3>

                                    <subtext>
                                        <?= htmlspecialchars(date("M j, Y g:i A", strtotime($event["start_time"]))) ?>
                                        —
                                        <?= htmlspecialchars(date("M j, Y g:i A", strtotime($event["end_time"]))) ?>
                                    </subtext>

                                    <p><strong>Location:</strong> <?= htmlspecialchars($event["location"] ?: "No location") ?></p>
                                    <p><?= nl2br(htmlspecialchars($event["description"] ?: "No description")) ?></p>

                                    <div class="eventActions">
                                        <a class="btn" href="events/edit.php?id=<?= urlencode($event["event_id"]) ?>">Edit</a>
                                        <button onclick="deleteConfirmation(this, <?= (int)$event['event_id'] ?>)" class="btn">Delete</button>
                                    </div>
                                </card>
                            <?php endforeach; ?>
                        </cardContainer>
                    <?php endif; ?>
                </card>
            </div>
        </div>
    </div>
    <script>
        window.EVENTS = <?= json_encode($events, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    </script>
    <script>
        const toggleCalendarBtn = document.getElementById("toggleCalendarBtn");
        const calendarWrapper = document.getElementById("calendarWrapper");

        if (toggleCalendarBtn && calendarWrapper) {
            toggleCalendarBtn.addEventListener("click", () => {
                const isHidden = calendarWrapper.classList.toggle("isHidden");
                toggleCalendarBtn.textContent = isHidden ? "Show Calendar" : "Hide Calendar";
            });
        }
    </script>
</body>
</html>