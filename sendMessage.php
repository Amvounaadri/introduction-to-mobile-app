<?php
include 'core/init.php';

if (isset($_POST['message'], $_POST['sender_id'], $_POST['receiver_id'])) {
    $message = $_POST['message'];
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];

    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (:sender_id, :receiver_id, :message)");
    $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
    $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);

    // Execute the statement, but don't output anything if successful
    if ($stmt->execute()) {
        http_response_code(200);  // Send an HTTP 200 status
    } else {
        http_response_code(500);  // Send an HTTP 500 status on error
        echo 'error';
    }
}
?>
