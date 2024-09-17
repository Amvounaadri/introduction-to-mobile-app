
<?php
// notification.php

// Check if the userEmail parameter is set
if (!isset($_GET['userEmail'])) {
    http_response_code(400);
    echo json_encode(array('error' => 'User email is required'));
    exit;
}

// Fetch notifications from the database based on the user's email
$userEmail = $_GET['userEmail'];

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'your_database_name');

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(array('error' => 'Failed to connect to database'));
    exit;
}

// Your database query to fetch notifications
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_email = ? AND read_status = 0");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

// Check if the query was successful
if (!$result) {
    http_response_code(500);
    echo json_encode(array('error' => 'Failed to fetch notifications'));
    exit;
}

// Fetch all notifications
$notifications = array();
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// Return the notifications as JSON
echo json_encode(array(
    'notification' => count($notifications),
    'messages' => count($notifications), // Assuming you want to display the total number of messages
    'message' => $notifications[0]['message'] ?? '' // Display the first notification message, or an empty string if no notifications
));

// Close the database connection
$stmt->close();
$conn->close();
?>
