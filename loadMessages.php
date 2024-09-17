<?php
include 'core/init.php';

if (isset($_POST['sender_id'], $_POST['receiver_id'])) {
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];

    // Prepare the SQL query to fetch messages based on sender and receiver ID
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE (sender_id = :sender_id AND receiver_id = :receiver_id) OR (sender_id = :receiver_id AND receiver_id = :sender_id) ORDER BY created_at ASC");
    $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
    $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // Fetch all messages
    $messages = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Loop through each message and output it with the correct class
    foreach ($messages as $message) {
        // Determine the sender and assign the appropriate class
        if ($message->sender_id == $sender_id) {
            // Message sent by the user
            echo '<li class="user-message">' . htmlspecialchars($message->message) . '</li>';
        } else {
            // Message sent by the friend
            echo '<li class="friend-message">' . htmlspecialchars($message->message) . '</li>';
        }
    }
}
?>