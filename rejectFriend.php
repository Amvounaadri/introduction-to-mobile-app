<?php
include 'core/init.php';

if (isset($_POST['friendship_id'])) {
    $friendship_id = $_POST['friendship_id'];

    // Call the rejectFriendRequest method
    if ($getFromU->rejectFriendRequest($friendship_id)) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'No friendship ID provided';
}
?>
