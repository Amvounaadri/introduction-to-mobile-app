<?php
include 'core/init.php';

if (isset($_POST['friend_id'])) {
    $friend_id = $_POST['friend_id'];
    $user_id = $_SESSION['user_id']; // Ensure $_SESSION['user_id'] is set

    if (isset($getFromU)) {
        // Attempt to send the friend request
        $success = $getFromU->sendFriendRequest($user_id, $friend_id);
        
        if ($success) {
            echo 'success';
        } else {
            // Fetch the error message from the PDO object
            $errorInfo = $getFromU->pdo->errorInfo();
            echo 'Database Error: ' . $errorInfo[2];
        }
    } else {
        echo 'Error: User object not found.';
    }
} else {
    echo 'No friend ID provided.';
}
?>
