<?php
// Include database connection and User class
include 'core/init.php';

if (isset($_GET['username'])) {
    $username = htmlspecialchars($_GET['username']);

    // Fetch user details based on username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if ($user) {
        echo '<div id="user-details-popup" class="popup-background">'; // Popup background
        echo '<div class="user-details-content">'; // Popup content

        // Close button
        echo '<span class="popup-close">&times;</span>';

        // Left side: User information
        echo '<div class="user-left">';
        echo '<img src="' . BASE_URL . $user->profileCover . '" class="user-cover">';
        echo '<img src="' . BASE_URL . $user->profileImage . '" class="user-image">';
        echo '<h3>' . htmlspecialchars($user->screenName) . '</h3>';
        echo '<p>@' . htmlspecialchars($user->username) . '</p>';
        echo '<p>' . htmlspecialchars($user->bio) . '</p>';
        echo '<a href="' . htmlspecialchars($user->website) . '" target="_blank">' . htmlspecialchars($user->website) . '</a>';
        echo '</div>';

        // Right side: CV or Jobs posted
        echo '<div class="user-right">';
        if ($user->usertype === 'Graduate') {
            // Display CV
            if (!empty($user->cv)) {
                echo '<h4>CV</h4>';
                echo '<iframe src="' . BASE_URL . $user->cv . '" width="400" height="600" style="border:none;"></iframe>';
                echo '<br><a href="' . BASE_URL . $user->cv . '" download><button>Download CV</button></a>';
            } else {
                echo '<p>No CV uploaded</p>';
            }
        } elseif ($user->usertype === 'Company') {
            // Fetch and display jobs posted by the user
            $stmt = $pdo->prepare("SELECT * FROM jobs WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user->user_id, PDO::PARAM_INT);
            $stmt->execute();
            $jobs = $stmt->fetchAll(PDO::FETCH_OBJ);

            echo '<h4>Jobs Posted</h4>';
            if ($jobs) {
                foreach ($jobs as $job) {
                    echo '<div class="job">';
                    echo '<h5>' . htmlspecialchars($job->job_title) . '</h5>';
                    echo '<p>' . htmlspecialchars($job->job_description) . '</p>';
                    echo '<button class="apply-btn" data-job="' . htmlspecialchars($job->job_id) . '" data-user="' . htmlspecialchars($user->user_id) . '">Apply</button>';
                    echo '</div>';
                }
            } else {
                echo '<p>No jobs posted</p>';
            }
        }
        echo '</div>'; // End right side
        echo '</div>'; // End user details content
        echo '</div>'; // End popup
    } else {
        echo 'User not found';
    }
} else {
    echo 'Invalid request';
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>THE ICTU GRAD CAREER PORTAL</title>
    <meta charset='UTF-8' />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,shrink-to-fit=no">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/bird.svg">
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/style-complete.css' />
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/style.css' />
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/font-awesome.css' />
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/bootstrap.css' />
    <script src='<?php echo BASE_URL; ?>assets/js/jquery-3.1.1.min.js'></script>
    <script>
        $(document).ready(function() {
            // Show the popup
            $('#user-details-popup').show();

            // Close the popup when the close button is clicked
            $('.popup-close').click(function() {
                $('#user-details-popup').hide();
            });

            // Close the popup when clicking outside of it
            $(window).click(function(event) {
                if ($(event.target).is('#user-details-popup')) {
                    $('#user-details-popup').hide();
                }
            });

            // Apply job functionality
            $(document).on('click', '.apply-btn', function() {
                var job_id = $(this).data('job');
                var company_id = $(this).data('user');

                $.ajax({
                    url: 'apply.php',
                    method: 'POST',
                    data: {
                        job_id: job_id,
                        company_id: company_id
                    },
                    success: function(response) {
                        console.log(response); // Log the response from the server
                        if (response.trim() === 'success') { // Use trim() to avoid any excess whitespace
                            $('.apply-btn[data-job="'+job_id+'"]').text('Applied').addClass('applied').attr('disabled', true);
                        } else if (response.trim() === 'already_applied') {
                            alert('You have already applied for this job.');
                        } else {
                            alert('Error applying for the job: ' + response);
                        }
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div id="user-details-popup" class="popup-background">
        <div class="user-details-content">
            <span class="popup-close">&times;</span>
            <div class="user-left">
                <img src="<?php echo BASE_URL . $user->profileCover; ?>" class="user-cover">
                <img src="<?php echo BASE_URL . $user->profileImage; ?>" class="user-image">
                <h3><?php echo htmlspecialchars($user->screenName); ?></h3>
                <p>@<?php echo htmlspecialchars($user->username); ?></p>
                <p><?php echo htmlspecialchars($user->bio); ?></p>
                <a href="<?php echo htmlspecialchars($user->website); ?>" target="_blank"><?php echo htmlspecialchars($user->website); ?></a>
            </div>
            <div class="user-right">
                <?php if ($user->usertype === 'Graduate') : ?>
                    <h4>CV</h4>
                    <?php if (!empty($user->cv)) : ?>
                        <embed src="<?php echo BASE_URL . $user->cv; ?>" width="400" height="600" type="application/pdf">
                    <?php else : ?>
                        <p>No CV uploaded</p>
                    <?php endif; ?>
                <?php elseif ($user->usertype === 'Company') : ?>
                    <h4>Jobs Posted</h4>
                    <?php
                    // Fetch and display jobs posted by the user
                    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE user_id = :user_id");
                    $stmt->bindParam(':user_id', $user->user_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $jobs = $stmt->fetchAll(PDO::FETCH_OBJ);
                    ?>
                    <?php if ($jobs) : ?>
                        <?php foreach ($jobs as $job) : ?>
                            <div class="job">
                                <h5><?php echo htmlspecialchars($job->job_title); ?></h5>
                                <p><?php echo htmlspecialchars($job->job_description); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>No jobs posted</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
