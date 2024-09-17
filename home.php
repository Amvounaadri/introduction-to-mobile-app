<!DOCTYPE HTML>
<html>

<?php
include 'core/init.php';
$user_id = $_SESSION['user_id'];
$user = $getFromU->userData( $user_id );

if ( $getFromU->loggedIn() === false ) {
    header( 'Location: '.BASE_URL.'index.php' );
}

// Handle job posting
if (isset($_POST['post_job'])) {
    $job_title = $getFromU->checkinput($_POST['job_title']);
    $job_description = $getFromU->checkinput($_POST['job_description']);
    $job_skills = $getFromU->checkinput($_POST['job_skills']);

    if (!empty($job_title) && !empty($job_description) && !empty($job_skills)) {
        $job_id = $getFromU->create('jobs', array(
            'job_title' => $job_title,
            'job_description' => $job_description,
            'skills_required' => $job_skills,
            'user_id' => $user_id,
            'created_at' => date('Y-m-d H:i:s')
        ));

        // Redirect or provide success message
        header('Location: home.php');
    } else {
        $error = 'Please fill in all fields.';
    }
}

if ( isset( $_POST['tweet'] ) ) {
    $status = $getFromU->checkinput( $_POST['status'] );
    $tweetImage = '';

    // Check if a file was uploaded
    if (!empty($_FILES['file']['name'])) {
        $file = $_FILES['file'];
        $fileName = basename($file['name']);
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileType = $file['type'];

        // Allowed file extensions
        $allowed = array('jpg', 'jpeg', 'png', 'gif');

        // Extract file extension
        $fileNameArray = explode('.', $fileName);
        $fileExt = strtolower(end($fileNameArray));

        if (in_array($fileExt, $allowed)) {
            if ($fileError === 0) {
                if ($fileSize <= 10000000) { // Limit the file size to 10MB
                    $fileNewName = uniqid('', true) . "." . $fileExt;
                    $fileDestination = 'uploads/tweetImages/' . $fileNewName;

                    // Check if the directory exists, if not create it
                    if (!file_exists('uploads/tweetImages/')) {
                        mkdir('uploads/tweetImages/', 0777, true);
                    }

                    // Move the uploaded file to the desired directory
                    if (move_uploaded_file($fileTmpName, $fileDestination)) {
                        $tweetImage = $fileDestination;
                    } else {
                        $imgError = "There was an error uploading your file.";
                    }
                } else {
                    $imgError = "Your file is too large.";
                }
            } else {
                $imgError = "There was an error uploading your file.";
            }
        } else {
            $imgError = "Invalid file type.";
        }
    }

    // Check if there were any image upload errors, or if no image was uploaded
    if (empty($imgError)) {
        // Insert tweet into the database, regardless of whether an image was uploaded
        $stmt = $pdo->prepare("INSERT INTO tweets (status, tweetBy, tweetImage, postedOn) VALUES (:status, :tweetBy, :tweetImage, NOW())");
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->bindParam(":tweetBy", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":tweetImage", $tweetImage, PDO::PARAM_STR);
        $stmt->execute();
    } else {
        // Handle the error if needed
        echo $imgError;
    }
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
                <img src="<?php echo BASE_URL; ?>assets/images/TY.png" alt="Custom Image" class="header-logo">
            </div>
        </header>


        <div class="grid-container">
            <!-- Left Sidebar -->
            <?php require 'left-sidebar.php'; ?>

            <div class="main">
                <div class=''>
                    <p class="page_title mb-0">.Home</p>

                    <?php if ($user->usertype == 'Company') { ?>
                        <!-- Job Posting Dashboard for Employers -->
                        <div class="company-dashboard">
                                <h2 class="animated-message">Company Dashboard</h2>
                            </div>

                            <div class="job_posting_box">
                                <div class="left-tweet">
                                    <img src="<?php echo $user->profileImage; ?>" alt="Profile Image" class="profile-image" style="width: 53px;height:53px;border-radius:50%;"/>
                                </div>

                                <div class="job_post_body">
                                    <form method="post" enctype="multipart/form-data">
                                        <input type="text" name="job_title" class="input-field" placeholder="Job Title" required>
                                        <textarea name="job_description" class="input-field" placeholder="Job Description" rows="4" required></textarea>
                                        <textarea name="job_skills" class="input-field" placeholder="Skills Required" rows="2" required></textarea>
                                        <button class="post-job-button" type="submit" name="post_job">Post Job</button>
                                    </form>
                                </div>
                            </div>


                        <div class="space" style="height:10px; width:100%; background:rgba(230, 236, 240, 0.5);"></div>
                    <?php } else { ?>
                        <!-- Default Tweet Box for Graduates -->
                        <div class="tweet_box tweet_add">
                            <div class="left-tweet ml-3">
                                <img class="mr-3" src="<?php echo $user->profileImage; ?>" style="width: 53px;height:53px;border-radius:50%;" />
                            </div>

                            <div class="tweet_body">
                                <form method="post" enctype="multipart/form-data">
                                    <textarea class="status" maxlength="1000" name="status" placeholder="What's new" rows="3" cols="100%" style="font-size:17px;"></textarea>

                                    <div class='t-fo-left tweet_icons-add'>
                                        <ul>
                                            <input type='file' name='file' id='file' />
                                            <li><label for='file'><i class='fa fa-image' aria-hidden='true'></i></label>
                               
                                            </li>
                                            <span class='tweet-error'><?php if ( isset( $error ) ) {
                                                echo $error;
                                            } else if ( isset( $imgError ) ) {
                                                echo '<br>' . $imgError;
                                            }
                                            ?></span>
                                            
                                                                        
                                        </ul>
                                        <button class="button_tweet" type="submit" name="tweet" style="outline:none;">Post Article</button>
                                            

                                    </div>

                                     </form>

                            </div>
                        </div>

                        <div class="space" style="height:10px; width:100%; background:rgba(230, 236, 240, 0.5);"></div>
                    <?php } ?>

                    <!-- Display Tweets and Jobs -->
                    <div class="tweets">
                        <?php 
                            if ($user->usertype == 'Company') {
                                $getFromT->displayPostedJobs($user_id);
                            }
                            
                            // Display tweets for both Graduate and Company users
                            $getFromT->tweets($user_id, 20);
                        ?>
                    </div>

                </div>
            </div>
            <?php include 'chat-window.php'; ?>
            <!-- Right Sidebar -->
            <?php require 'right-sidebar.php'; ?>
        </div>

    <script type='text/javascript' src='<?php echo BASE_URL; ?>assets/js/follow.js'></script>

    <script src='<?php echo BASE_URL; ?>assets/js/jquery-3.1.1.min.js'></script>
    <script src='<?php echo BASE_URL; ?>assets/js/popper.min.js'></script>
    <script src='<?php echo BASE_URL; ?>assets/js/bootstrap.min.js'></script>
    <!-- JavaScript to Handle Apply Button Click -->
    <script type="text/javascript">
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



    const header = document.querySelector('.grad-header');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 0) {
            header.classList.add('hidden'); // Add hidden class when scrolling down
        } else {
            header.classList.remove('hidden'); // Remove hidden class when at the top
        }
    });

    </script>


</body>

</html>
    