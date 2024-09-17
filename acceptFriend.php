<?php
include 'core/init.php';

if (isset($_POST['friendship_id'])) {
    $friendship_id = $_POST['friendship_id'];

    // Call the acceptFriendRequest method
    if ($getFromU->acceptFriendRequest($friendship_id)) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'No friendship ID provided';
}
?>
