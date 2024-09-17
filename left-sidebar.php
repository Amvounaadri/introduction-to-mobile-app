<?php
if (isset($_GET['username']) === true && empty($_GET['username']) === false) {
    $username = $getFromU->checkInput($_GET['username']);
    $profileId = $getFromU->userIdByUsername($username);
    $profileData = $getFromU->userData($profileId);
    $user_id = @$_SESSION['user_id'];
    $user = $getFromU->userData($user_id);

    if (!$profileData) {
        header('Location: ' . BASE_URL . 'index.php');
    }
}
?>
<div class="sidebar">
    <!-- Add your image at the top of the sidebar -->
    <img src="<?php echo BASE_URL; ?>assets/images/TY.png" alt="Custom Image" class="header-logo" style="width: 100%; height: auto;"/>

    <ul>
        <?php if ($getFromU->loggedIn() === true) { ?>
            <div class="media" style="margin-top: 10px;">
                <li class="media-inner">
                    
                    <a href="<?php echo BASE_URL . $user->username; ?>">
                    <a href="viewuserdetail.php?username=<?php echo $user->username; ?>">

                        <img class="mr-1" src="<?php echo BASE_URL; ?><?php echo $user->profileImage; ?>" style="height: 40px; width: 40px; border-radius: 50%;" />
                        <div class="media-body">
                            <h5 class="mt-0 mb-1">
                                <a href="<?php echo $user->username; ?>"><span><?php echo '<b>' . $user->screenName . '</b>'; ?></span></a>
                            </h5>
                            <span class="text-muted"><?php echo "@" . $user->username; ?></span>
                        </div>
                    </a>
                </li>
            </div>
        <?php } ?>
    </ul>


    <ul style="list-style: none;">
        <li class="active_menu"><a href='<?php echo BASE_URL; ?>home.php'><i class="fa fa-home" style="color: #f62626;"></i><span style="color: #f62626;">Home</span></a></li>
        <?php if ($getFromU->loggedIn() === true) { ?>
            <li><a href='<?php echo BASE_URL; ?>explore.php'><i class="fa fa-search"></i><span>Explore Jobs</span></a></li>
            
            <li><a href="<?php echo BASE_URL;?>i/notifications"><i class="fa fa-envelope" aria-hidden='true'></i><span>Applications</span></a></li>
            <li><a href='<?php echo BASE_URL; ?>social.php'><i class="fa fa-bell" aria-hidden="true"></i><span>Social</span></a></li>
            <li>
                    <a href="<?php echo BASE_URL; ?><?php echo "profile.php?username=".$user->username.""; ?>">
                        <i class="fa fa-user"></i><span>Profile</span>
                    </a>
                </li>
            <li><a href='<?php echo BASE_URL; ?>settings/account'><i class="fa fa-cog"></i><span>Settings</span></a></li>
            <li><a href='<?php echo BASE_URL; ?>includes/logout.php'><i class="fa fa-power-off"></i><span>Logout</span></a></li>
        <?php } ?>
        <?php if ($getFromU->loggedIn() === false) { ?>
            <a href='<?php echo BASE_URL; ?>' style="text-decoration: none;">
                <li style="padding: 10px 40px;"><button class="sidebar_tweet button" style="outline: none;">Login</button></li>
            </a>
        <?php } ?>
    </ul>
</div>

<style>
    .sidebar-image {
        width: 100%; /* Make the image responsive */
        height: auto; /* Maintain aspect ratio */
        margin-bottom: 10px; /* Add some spacing below the image */
    }
</style>
