<!DOCTYPE html>
<html>
<?php
// Your PHP code here...

// Notification pop-up HTML code
echo '<div id="notification-pop-up" style="display: none;">
    <div class="toast">
        <div class="toast-header">
            <strong class="mr-auto">Notification</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            <p id="notification-message"></p>
        </div>
    </div>
</div>';

// Rest of your PHP code...
?>
<?php 
include '../../core/init.php';

$user_id = $_SESSION['user_id'];
$user = $getFromU->userData($user_id);
$friendRequests = $getFromU->getFriendRequests($user_id);

if ($getFromU->loggedIn() === false) {
    header('Location: index.php');
    exit; // Make sure to exit after redirection
}

// Check user type
$isGraduate = $user->usertype === 'Graduate';
$isCompany = $user->usertype === 'Company';

// Retrieve job applications for graduates or applications for companies
if ($isGraduate) {
    $appliedJobs = $getFromU->getAppliedJobs($user_id); // Assuming this method exists in User class
} elseif ($isCompany) {
    $applications = $getFromU->getApplications($user_id); // Assuming this method exists in User class
}
?>


<head>
    <title>Notifications</title>
    <meta charset="UTF-8" />
    <link rel='shortcut icon' type='image/x-icon' href='<?php echo BASE_URL; ?>./assets/images/GC.png'>
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/images/bird.svg">
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>./assets/css/style-complete.css' />
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>./assets/css/style.css' />
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>./assets/css/bootstrap.css' />
    <script src='<?php echo BASE_URL; ?>./assets/js/jquery-3.1.1.min.js'></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css" />

</head>

<body>
<header class="grad-header">
    <div class="header-left">
        <h1>Grad Career Portal</h1>
        <a href="<?php echo BASE_URL; ?>home.php"></a>
    </div>
    <div class="header-right">
        <ul class="header-icons">
            <li>
                <a href="<?php echo BASE_URL;?>i/notifications">
                    <i class="fa fa-bell" aria-hidden="true"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?><?php echo "profile.php?username=".$user->username; ?>">
                    <i class="fa fa-user"></i>
                </a>
            </li>
            <li>
                <a href='<?php echo BASE_URL; ?>settings/account'>
                    <i class="fa fa-cog"></i>
                </a>
            </li>
        </ul>
        <img src="<?php echo BASE_URL; ?>assets/images/TY.png" alt="Custom Image" class="header-logo">
        
    </div>
</header>

<div class="grid-container">
    <?php require '../../left-sidebar.php'; ?>
    

    <div class="main">
        <p class="page_title mb-0">Notifications</p>
        
        <div class="notification-card">
            <?php if ($isGraduate): ?>
                <h2>Your Applied Jobs</h2>
                <ul>
                    <?php if (!empty($appliedJobs)): ?>
                        <?php foreach($appliedJobs as $job): ?>
                        <li class="application-item">
                                <?php echo htmlspecialchars($job['job_title']); ?> - <?php echo htmlspecialchars($getFromU->timeAgo($job['applied_at'])); ?>
                                <div class="button-group">
                                    <button class="reject-button" data-application-id="<?php echo htmlspecialchars($application['application_id']); ?>">Cancel</button>
                                </div>
                        </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No applied jobs found.</li>
                    <?php endif; ?>
                </ul>
            <?php elseif ($isCompany): ?>
                <h2>Applications Received</h2>
                    <ul>
                        <?php if (!empty($applications)): ?>
                            <?php foreach($applications as $application): ?>
                                <li class="application-item">
                                    <?php echo htmlspecialchars($application['graduateName']); ?> applied for <?php echo htmlspecialchars($application['job_title']); ?>
                                    <div class="button-group">
                                        <button class="apply-button" data-application-id="<?php echo htmlspecialchars($application['application_id']); ?>">Validate</button>
                                        <button class="reject-button" data-application-id="<?php echo htmlspecialchars($application['application_id']); ?>">Reject</button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No applications received.</li>
                        <?php endif; ?>
                    </ul>
                <p>No applications found.</p>
            <?php endif; ?>
        </div>

        <div class="notification-card">
            <h2>Friend Requests</h2>
            <ul>
                <?php foreach ($friendRequests as $request): ?>
                    <li>
                        <?php echo htmlspecialchars($request->screenName); ?> wants to be your friend.
                        <button class="accept-friend-btn" data-friendship-id="<?php echo htmlspecialchars($request->id); ?>">Accept</button>
                        <button class="reject-friend-btn" data-friendship-id="<?php echo htmlspecialchars($request->id); ?>">Reject</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="loading-div">
            <img id="loader" src="<?php echo BASE_URL;?>assets/images/loading.svg" style="display: none;" />
        </div>
    </div>
    <?php include '../../chat-window.php'; ?>
    <?php require '../../right-sidebar.php'; ?>
</div>
<script>
$(document).ready(function() {
    // Accept Friend Request
    $('.accept-friend-btn').click(function() {
        var friendshipId = $(this).data('friendship-id');
        var listItem = $(this).closest('li'); // Find the corresponding list item

        $.post('<?php echo BASE_URL; ?>acceptFriend.php', {friendship_id: friendshipId}, function(response) {
            if(response.trim() === 'success') {
                // Remove the friend request notification
                listItem.fadeOut();
                // Optionally display a success message
            } else {
                alert('accepting friend request: ' + response);
            }
        }).fail(function() {
            alert('An error occurred. Please try again.');
        });
    });

    // Reject Friend Request
    $('.reject-friend-btn').click(function() {
                var friendshipId = $(this).data('friendship-id');
                var listItem = $(this).closest('li'); // Find the corresponding list item

                $.post('<?php echo BASE_URL; ?>rejectFriend.php', {friendship_id: friendshipId}, function(response) {
                    if(response.trim() === 'success') {
                        // Remove the friend request notification
                        listItem.fadeOut();
                        // Optionally display a success message
                    } else {
                        alert('rejecting friend request: ' + response);
                    }
                }).fail(function() {
                    alert('An error occurred. Please try again.');
                });
            });
        });
        const header = document.querySelector('.grad-header');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 0) {
                header.classList.add('hidden'); // Add hidden class when scrolling down
            } else {
                header.classList.remove('hidden'); // Remove hidden class when at the top
            }
        });

</script>

<script src='<?php echo BASE_URL;?>assets/js/jquery-3.1.1.min.js'></script>
<script src='<?php echo BASE_URL;?>assets/js/bootstrap.min.js'></script>
<script type='<?php echo BASE_URL; ?>assets/js/follow.js'></script>
<script type='text/javascript' src='<?php echo BASE_URL; ?>assets/js/follow.js'></script>
<script src='<?php echo BASE_URL; ?>assets/js/jquery-3.1.1.min.js'></script>
<script src='<?php echo BASE_URL; ?>assets/js/popper.min.js'></script>
<script src='<?php echo BASE_URL; ?>assets/js/bootstrap.min.js'></script>

</body>
</html>
