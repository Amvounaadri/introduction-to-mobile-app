<!DOCTYPE HTML>
<html>

<?php
include 'core/init.php';
$user_id = $_SESSION['user_id'];
$user = $getFromU->userData( $user_id );
$friends = $getFromU->getFriends($user_id);

if ( $getFromU->loggedIn() === false ) {
    header( 'Location: '.BASE_URL.'index.php' );
}

?>

<head>
    <title>THE ICTU GRAD CAREER PORTAL</title>
    <meta charset='UTF-8' />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,shrink-to-fit=no">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/images/bird.svg">
    <link rel = 'stylesheet' href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css'/>  
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/style-complete.css' />
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/style.css' />
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/font-awesome.css' />
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/bootstrap.css' />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css" />   
    <script src='<?php echo BASE_URL; ?>assets/js/jquery-3.1.1.min.js'></script>
    <script src = 'https://code.jquery.com/jquery-3.2.1.min.js'></script>

</head>

<body>

    <header class="grad-header">
        <div class="header-left">
        <h1>Grad Career Portal</h1>
        </div>
        <div class="header-right">
            <ul class="header-icons">
                <li>
                    <a href="<?php echo BASE_URL;?>i/notifications">
                        <i class="fa fa-bell" aria-hidden="true"></i>
                    </a>
                </li>
                <li id='messagePopup'>
                    <a>
                        <i class="fa fa-envelope" aria-hidden='true'></i>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?><?php echo "profile.php?username=".$user->username.""; ?>">
                        <i class="fa fa-user"></i>
                    </a>
                </li>
                <li>
                    <a href='<?php echo BASE_URL; ?>settings/account'>
                        <i class="fa fa-cog"></i>
                    </a>
                </li>
            </ul>
            <img src="assets/images/TY.png" alt="Custom Image" class="header-logo">
        </div>
    </header>

    <div class="grid-container">
        <?php require 'left-sidebar.php'; ?>

        <div class="main">
            <p class="page_title mb-0">Your Friends</p>
            <div class="friends-list">
                <ul>
                    <?php if (!empty($friends)): ?>
                        <?php foreach ($friends as $friend): ?>
                            <li>
                                <img src="<?php echo BASE_URL . $friend->profileImage; ?>" alt="<?php echo htmlspecialchars($friend->screenName); ?>" style="width: 40px; height: 40px; border-radius: 50%;">
                                <span><?php echo htmlspecialchars($friend->screenName); ?></span>
                                <!-- Add message icon button -->
                                <button class="message-btn" data-friend-id="<?php echo $friend->user_id; ?>" data-friend-name="<?php echo htmlspecialchars($friend->screenName); ?>" data-friend-img="<?php echo BASE_URL . $friend->profileImage; ?>">
                                    <i class="fa fa-envelope"></i>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>You have no friends yet.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Include the chat window -->
        <?php include 'chat-window.php'; ?>
        <?php require 'right-sidebar.php'; ?>
    </div>

    <script type='text/javascript' src='<?php echo BASE_URL; ?>assets/js/follow.js'></script>
    <script src='<?php echo BASE_URL; ?>assets/js/jquery-3.1.1.min.js'></script>
    <script src='<?php echo BASE_URL; ?>assets/js/popper.min.js'></script>
    <script src='<?php echo BASE_URL; ?>assets/js/bootstrap.min.js'></script>
</body>

</html>
